<?php
require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Guid.php';
require_once __DIR__ . '/ConditionEvaluator.php';

class WorkflowExecutionEngine {
  private Db $db;
  private ConditionEvaluator $evaluator;

  public function __construct(Db $db) {
    $this->db = $db;
    $this->evaluator = new ConditionEvaluator($db);
  }

  public function runOnce(int $limitWorkflows = 100, int $limitRecords = 200): array {
    $now = date('Y-m-d H:i:s');
    $workflows = $this->db->all("
      SELECT *
      FROM limocrm_workflows
      WHERE deleted=0 AND status='Active'
      ORDER BY date_entered DESC
      LIMIT " . (int)$limitWorkflows . "
    ");

    $stats = [
      'workflows' => count($workflows),
      'evaluated_records' => 0,
      'started' => 0,
      'resumed' => 0,
      'completed' => 0,
      'failed' => 0,
      'waiting' => 0,
    ];

    // Resume waiting/runnable logs first
    $runnable = $this->db->all("
      SELECT *
      FROM limocrm_workflow_execution_log
      WHERE deleted=0
        AND status IN ('running','waiting')
        AND (next_run_at IS NULL OR next_run_at <= '$now')
      ORDER BY next_run_at IS NULL DESC, next_run_at ASC
      LIMIT 100
    ");

    foreach ($runnable as $logRow) {
      $stats['resumed']++;
      $out = $this->advanceLog($logRow);
      $stats[$out['final_status']] = ($stats[$out['final_status']] ?? 0) + 1;
    }

    foreach ($workflows as $wf) {
      $module = (string)$wf['module_name'];
      $trigger = (string)$wf['trigger_type'];
      $triggerField = (string)($wf['trigger_field'] ?? '');
      $triggerValue = (string)($wf['trigger_value'] ?? '');
      $runOnce = (int)($wf['run_once'] ?? 1) ? 1 : 0;

      $records = $this->fetchTriggeredRecords($module, $trigger, $triggerField, $triggerValue, $limitRecords);
      foreach ($records as $rec) {
        $stats['evaluated_records']++;

        $recordId = (string)($rec['id'] ?? $rec['id_c'] ?? '');
        if (!$recordId) continue;

        // run_once guard
        if ($runOnce) {
          $exists = $this->db->one("
            SELECT id, status
            FROM limocrm_workflow_execution_log
            WHERE workflow_id='".$this->db->esc($wf['id'])."'
              AND record_id='".$this->db->esc($recordId)."'
              AND deleted=0
            LIMIT 1
          ");
          if ($exists) continue;
        }

        if (!$this->evaluator->evaluate($wf['id'], $rec)) {
          continue;
        }

        // Start log entry
        $logId = Guid::v4();
        $this->db->q("
          INSERT INTO limocrm_workflow_execution_log
          (id, workflow_id, record_id, module_name, current_action_index, status, next_run_at, last_error, started_at, completed_at, updated_at, deleted)
          VALUES
          ('".$this->db->esc($logId)."', '".$this->db->esc($wf['id'])."', '".$this->db->esc($recordId)."', '".$this->db->esc($module)."', 0, 'running', NULL, NULL, '$now', NULL, '$now', 0)
        ");
        $stats['started']++;

        $logRow = $this->db->one("SELECT * FROM limocrm_workflow_execution_log WHERE id='".$this->db->esc($logId)."' LIMIT 1");
        if ($logRow) {
          $out = $this->advanceLog($logRow);
          $stats[$out['final_status']] = ($stats[$out['final_status']] ?? 0) + 1;
        }
      }
    }

    return $stats;
  }

  private function fetchTriggeredRecords(string $module, string $trigger, string $field, string $value, int $limit): array {
    // Minimal initial support: Leads and AOS_Quotes.
    // For triggers we rely on SuiteCRM standard timestamps:
    // - leads.date_entered / date_modified
    $limit = (int)$limit;
    $nowMinus5 = date('Y-m-d H:i:s', time() - 300);

    if ($module === 'Leads') {
      if ($trigger === 'on_create') {
        return $this->db->all("
          SELECT l.*, c.*
          FROM leads l
          LEFT JOIN leads_cstm c ON c.id_c = l.id
          WHERE l.deleted=0 AND l.date_entered >= '$nowMinus5'
          ORDER BY l.date_entered DESC
          LIMIT $limit
        ");
      }

      if ($trigger === 'on_update') {
        return $this->db->all("
          SELECT l.*, c.*
          FROM leads l
          LEFT JOIN leads_cstm c ON c.id_c = l.id
          WHERE l.deleted=0 AND l.date_modified >= '$nowMinus5'
          ORDER BY l.date_modified DESC
          LIMIT $limit
        ");
      }

      if ($trigger === 'on_field_change') {
        // No true audit table parsing in v1; approximate by date_modified window + field equals value.
        $f = $this->db->esc($field);
        $v = $this->db->esc($value);
        return $this->db->all("
          SELECT l.*, c.*
          FROM leads l
          LEFT JOIN leads_cstm c ON c.id_c = l.id
          WHERE l.deleted=0 AND l.date_modified >= '$nowMinus5'
            AND (
              l.$f = '$v'
              OR c.$f = '$v'
            )
          ORDER BY l.date_modified DESC
          LIMIT $limit
        ");
      }
    }

    return [];
  }

  private function advanceLog(array $logRow): array {
    $now = date('Y-m-d H:i:s');
    $logId = (string)$logRow['id'];
    $workflowId = (string)$logRow['workflow_id'];
    $module = (string)($logRow['module_name'] ?? '');
    $recordId = (string)$logRow['record_id'];
    $idx = (int)($logRow['current_action_index'] ?? 0);

    $actions = $this->db->all("
      SELECT *
      FROM limocrm_workflow_actions
      WHERE workflow_id='".$this->db->esc($workflowId)."' AND deleted=0
      ORDER BY sort_order ASC
    ");

    if (!count($actions)) {
      $this->db->q("UPDATE limocrm_workflow_execution_log SET status='failed', last_error='No actions configured', updated_at='$now' WHERE id='".$this->db->esc($logId)."'");
      return ['final_status' => 'failed'];
    }

    $maxSteps = 25;
    $steps = 0;
    while ($idx < count($actions) && $steps < $maxSteps) {
      $steps++;
      $a = $actions[$idx];
      $type = (string)$a['action_type'];

      if ($type === 'delay') {
        $val = (int)($a['delay_value'] ?? 0);
        if ($val <= 0) $val = 1;
        $unit = (string)($a['delay_unit'] ?? 'minutes');
        $next = $this->addDelay($val, $unit);
        $this->db->q("
          UPDATE limocrm_workflow_execution_log
          SET status='waiting', next_run_at='".$this->db->esc($next)."', current_action_index=".(int)$idx.", updated_at='$now'
          WHERE id='".$this->db->esc($logId)."'
        ");
        return ['final_status' => 'waiting'];
      }

      $ok = $this->executeAction($type, $a, $module, $recordId);
      if (!$ok['success']) {
        $this->db->q("
          UPDATE limocrm_workflow_execution_log
          SET status='failed', last_error='".$this->db->esc($ok['message'])."', updated_at='$now'
          WHERE id='".$this->db->esc($logId)."'
        ");
        return ['final_status' => 'failed'];
      }

      $idx++;
      $this->db->q("
        UPDATE limocrm_workflow_execution_log
        SET status='running', next_run_at=NULL, current_action_index=".(int)$idx.", updated_at='$now'
        WHERE id='".$this->db->esc($logId)."'
      ");
    }

    $this->db->q("
      UPDATE limocrm_workflow_execution_log
      SET status='completed', completed_at='$now', updated_at='$now'
      WHERE id='".$this->db->esc($logId)."'
    ");
    return ['final_status' => 'completed'];
  }

  private function executeAction(string $type, array $a, string $module, string $recordId): array {
    if ($type === 'send_email') {
      $tmpl = (string)($a['email_template_id'] ?? '');
      if (!$tmpl) return ['success' => false, 'message' => 'send_email missing email_template_id'];
      $resp = $this->suiteSendEmail($module, $recordId, $tmpl);
      if (!($resp['success'] ?? false)) return ['success' => false, 'message' => (string)($resp['message'] ?? 'Email send failed')];
      return ['success' => true, 'message' => 'sent'];
    }

    if ($type === 'update_field' || $type === 'change_status') {
      $field = (string)($a['target_field'] ?? '');
      $value = (string)($a['target_value'] ?? '');
      if (!$field) return ['success' => false, 'message' => 'update_field missing target_field'];
      return $this->updateRecordField($module, $recordId, $field, $value);
    }

    if ($type === 'webhook') {
      $url = (string)($a['webhook_url'] ?? '');
      $method = strtoupper((string)($a['webhook_method'] ?? 'POST'));
      if (!$url) return ['success' => false, 'message' => 'webhook missing url'];
      return $this->fireWebhook($url, $method, ['module' => $module, 'record_id' => $recordId]);
    }

    // create_task / send_notification: v1 stores config but does not execute (can be extended)
    if ($type === 'create_task' || $type === 'send_notification') {
      return ['success' => true, 'message' => 'noop'];
    }

    return ['success' => false, 'message' => 'Unknown action type: ' . $type];
  }

  private function addDelay(int $val, string $unit): string {
    $u = strtolower(trim($unit));
    $sec = 60 * $val;
    if ($u === 'hours') $sec = 3600 * $val;
    if ($u === 'days') $sec = 86400 * $val;
    if ($u === 'weeks') $sec = 604800 * $val;
    return date('Y-m-d H:i:s', time() + $sec);
  }

  private function suiteSendEmail(string $module, string $recordId, string $templateId): array {
    $endpoint = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
      'action' => 'send_workflow_email',
      'module_name' => $module,
      'record_id' => $recordId,
      'email_template_id' => $templateId,
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) return ['success' => false, 'message' => $err ?: 'cURL error'];
    $decoded = json_decode($resp, true);
    return is_array($decoded) ? $decoded : ['success' => false, 'message' => 'Non-JSON response'];
  }

  private function updateRecordField(string $module, string $recordId, string $field, string $value): array {
    // Minimal initial support for Leads via leads / leads_cstm.
    if ($module !== 'Leads') return ['success' => false, 'message' => 'update_field only implemented for Leads in v1'];

    $rid = $this->db->esc($recordId);
    $f = $this->db->esc($field);
    $v = $this->db->esc($value);

    // Try base table first
    $try1 = @mysqli_query($this->db->conn, "UPDATE leads SET $f='$v', date_modified='".date('Y-m-d H:i:s')."' WHERE id='$rid' AND deleted=0");
    if ($try1 !== false && mysqli_affected_rows($this->db->conn) >= 0) {
      return ['success' => true, 'message' => 'updated'];
    }

    // Fallback cstm
    $try2 = @mysqli_query($this->db->conn, "UPDATE leads_cstm SET $f='$v' WHERE id_c='$rid'");
    if ($try2 !== false && mysqli_affected_rows($this->db->conn) >= 0) {
      return ['success' => true, 'message' => 'updated'];
    }

    return ['success' => false, 'message' => 'Failed to update field'];
  }

  private function fireWebhook(string $url, string $method, array $payload): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($method === 'POST') {
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES));
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($resp === false) return ['success' => false, 'message' => $err ?: 'cURL error'];
    if ($code >= 400) return ['success' => false, 'message' => 'Webhook HTTP ' . $code];
    return ['success' => true, 'message' => 'ok'];
  }
}


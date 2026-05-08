<?php
require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Guid.php';
require_once __DIR__ . '/WorkflowExecutionEngine.php';

class WorkflowController {
  private Db $db;

  public function __construct(Db $db) {
    $this->db = $db;
  }

  public function list(): array {
    $rows = $this->db->all("
      SELECT
        w.*,
        (SELECT COUNT(1) FROM limocrm_workflow_conditions c WHERE c.workflow_id=w.id AND c.deleted=0) AS conditions_count,
        (SELECT COUNT(1) FROM limocrm_workflow_actions a WHERE a.workflow_id=w.id AND a.deleted=0) AS actions_count
      FROM limocrm_workflows w
      WHERE w.deleted=0
      ORDER BY COALESCE(w.date_modified, w.date_entered) DESC
    ");
    return ['success' => true, 'rows' => $rows];
  }

  public function get(string $id): array {
    $wid = $this->db->esc($id);
    $wf = $this->db->one("SELECT * FROM limocrm_workflows WHERE id='$wid' AND deleted=0 LIMIT 1");
    if (!$wf) return ['success' => false, 'message' => 'Not found'];

    $conds = $this->db->all("
      SELECT id, condition_field AS field, condition_operator AS operator, condition_value AS value, condition_group AS `group`, sort_order
      FROM limocrm_workflow_conditions
      WHERE workflow_id='$wid' AND deleted=0
      ORDER BY condition_group ASC, sort_order ASC
    ");
    $acts = $this->db->all("
      SELECT *
      FROM limocrm_workflow_actions
      WHERE workflow_id='$wid' AND deleted=0
      ORDER BY sort_order ASC
    ");

    return ['success' => true, 'workflow' => $wf, 'conditions' => $conds, 'actions' => $acts];
  }

  public function create(array $payload): array {
    return $this->upsert(null, $payload, true);
  }

  public function update(string $id, array $payload): array {
    return $this->upsert($id, $payload, false);
  }

  public function delete(string $id): array {
    $wid = $this->db->esc($id);
    $now = date('Y-m-d H:i:s');
    $this->db->q("UPDATE limocrm_workflows SET deleted=1, date_modified='$now' WHERE id='$wid'");
    $this->db->q("UPDATE limocrm_workflow_conditions SET deleted=1 WHERE workflow_id='$wid'");
    $this->db->q("UPDATE limocrm_workflow_actions SET deleted=1 WHERE workflow_id='$wid'");
    return ['success' => true];
  }

  public function moduleFields(string $module): array {
    $module = trim($module);
    if ($module === '') return ['success' => false, 'message' => 'module required'];

    // Map module -> base + cstm tables
    $map = [
      'Leads' => ['leads', 'leads_cstm', 'id', 'id_c'],
      'AOS_Quotes' => ['aos_quotes', 'aos_quotes_cstm', 'id', 'id_c'],
      'Contacts' => ['contacts', 'contacts_cstm', 'id', 'id_c'],
    ];
    if (!isset($map[$module])) return ['success' => false, 'message' => 'Unsupported module'];

    [$base, $cstm] = $map[$module];
    $dbName = $this->db->esc($this->db->one("SELECT DATABASE() AS db")['db'] ?? '');

    $cols = [];
    foreach ([$base, $cstm] as $t) {
      $tq = $this->db->esc($t);
      $rows = $this->db->all("
        SELECT COLUMN_NAME, DATA_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$dbName'
          AND TABLE_NAME = '$tq'
        ORDER BY ORDINAL_POSITION ASC
      ");
      foreach ($rows as $r) {
        $name = (string)$r['COLUMN_NAME'];
        if ($name === 'deleted') continue;
        if (!isset($cols[$name])) {
          $cols[$name] = ['name' => $name, 'type' => (string)$r['DATA_TYPE']];
        }
      }
    }

    return ['success' => true, 'module' => $module, 'fields' => array_values($cols)];
  }

  public function emailTemplates(): array {
    $rows = $this->db->all("
      SELECT id, name
      FROM email_templates
      WHERE deleted=0
      ORDER BY date_modified DESC
      LIMIT 500
    ");
    return ['success' => true, 'rows' => $rows];
  }

  public function executeNow(string $id): array {
    $wid = $this->db->esc($id);
    $wf = $this->db->one("SELECT * FROM limocrm_workflows WHERE id='$wid' AND deleted=0 LIMIT 1");
    if (!$wf) return ['success' => false, 'message' => 'Not found'];

    $engine = new WorkflowExecutionEngine($this->db);
    $stats = $engine->runOnce(1, 50);
    return ['success' => true, 'stats' => $stats];
  }

  public function logs(string $id): array {
    $wid = $this->db->esc($id);
    $rows = $this->db->all("
      SELECT *
      FROM limocrm_workflow_execution_log
      WHERE workflow_id='$wid' AND deleted=0
      ORDER BY COALESCE(updated_at, started_at) DESC
      LIMIT 200
    ");
    return ['success' => true, 'rows' => $rows];
  }

  private function upsert(?string $id, array $payload, bool $isCreate): array {
    $now = date('Y-m-d H:i:s');

    $name = trim((string)($payload['workflow_name'] ?? $payload['name'] ?? ''));
    $module = trim((string)($payload['module_name'] ?? $payload['module'] ?? ''));
    $triggerType = trim((string)($payload['trigger_type'] ?? ''));
    $triggerField = trim((string)($payload['trigger_field'] ?? ''));
    $triggerValue = trim((string)($payload['trigger_value'] ?? ''));
    $status = trim((string)($payload['status'] ?? 'Active'));
    $description = trim((string)($payload['description'] ?? ''));
    $runOnce = (int)!!($payload['run_once'] ?? 1);
    $createdBy = trim((string)($payload['created_by'] ?? $payload['user_id'] ?? ''));

    $conditions = $payload['conditions'] ?? [];
    $actions = $payload['actions'] ?? [];

    if ($name === '' || $module === '' || $triggerType === '') {
      return ['success' => false, 'message' => 'workflow_name, module_name, trigger_type are required'];
    }
    if (!is_array($conditions) || !count($conditions)) {
      return ['success' => false, 'message' => 'At least one condition is required'];
    }
    if (!is_array($actions) || !count($actions)) {
      return ['success' => false, 'message' => 'At least one action is required'];
    }

    $wid = $isCreate ? Guid::v4() : (string)$id;
    $widQ = $this->db->esc($wid);

    if ($isCreate) {
      $this->db->q("
        INSERT INTO limocrm_workflows
        (id, workflow_name, module_name, trigger_type, trigger_field, trigger_value, status, description, run_once, created_by, date_entered, date_modified, deleted)
        VALUES
        ('$widQ',
         '".$this->db->esc($name)."',
         '".$this->db->esc($module)."',
         '".$this->db->esc($triggerType)."',
         ".($triggerField !== '' ? "'".$this->db->esc($triggerField)."'" : "NULL").",
         ".($triggerValue !== '' ? "'".$this->db->esc($triggerValue)."'" : "NULL").",
         '".$this->db->esc($status)."',
         ".($description !== '' ? "'".$this->db->esc($description)."'" : "NULL").",
         $runOnce,
         ".($createdBy !== '' ? "'".$this->db->esc($createdBy)."'" : "NULL").",
         '$now',
         '$now',
         0
        )
      ");
    } else {
      // Update definition
      $this->db->q("
        UPDATE limocrm_workflows SET
          workflow_name='".$this->db->esc($name)."',
          module_name='".$this->db->esc($module)."',
          trigger_type='".$this->db->esc($triggerType)."',
          trigger_field=".($triggerField !== '' ? "'".$this->db->esc($triggerField)."'" : "NULL").",
          trigger_value=".($triggerValue !== '' ? "'".$this->db->esc($triggerValue)."'" : "NULL").",
          status='".$this->db->esc($status)."',
          description=".($description !== '' ? "'".$this->db->esc($description)."'" : "NULL").",
          run_once=$runOnce,
          date_modified='$now'
        WHERE id='$widQ' AND deleted=0
      ");

      // Replace: mark existing as deleted
      $this->db->q("UPDATE limocrm_workflow_conditions SET deleted=1 WHERE workflow_id='$widQ'");
      $this->db->q("UPDATE limocrm_workflow_actions SET deleted=1 WHERE workflow_id='$widQ'");
    }

    // Insert conditions
    $i = 0;
    foreach ($conditions as $c) {
      $i++;
      $cid = Guid::v4();
      $field = trim((string)($c['field'] ?? $c['condition_field'] ?? ''));
      $op = trim((string)($c['operator'] ?? $c['condition_operator'] ?? ''));
      $val = (string)($c['value'] ?? $c['condition_value'] ?? '');
      $group = (int)($c['group'] ?? $c['condition_group'] ?? 1);
      if ($field === '' || $op === '') continue;

      $this->db->q("
        INSERT INTO limocrm_workflow_conditions
        (id, workflow_id, condition_field, condition_operator, condition_value, condition_group, sort_order, deleted)
        VALUES
        ('".$this->db->esc($cid)."', '$widQ', '".$this->db->esc($field)."', '".$this->db->esc($op)."',
         ".(trim((string)$val) !== '' ? "'".$this->db->esc((string)$val)."'" : "NULL").",
         $group, $i, 0)
      ");
    }

    // Insert actions
    $j = 0;
    foreach ($actions as $a) {
      $j++;
      $aid = Guid::v4();
      $type = trim((string)($a['type'] ?? $a['action_type'] ?? ''));
      if ($type === '') continue;

      $emailTemplateId = trim((string)($a['email_template_id'] ?? ''));
      $delayValue = isset($a['delay_value']) ? (int)$a['delay_value'] : null;
      $delayUnit = trim((string)($a['delay_unit'] ?? ''));
      $targetField = trim((string)($a['target_field'] ?? ''));
      $targetValue = trim((string)($a['target_value'] ?? ''));
      $taskSubject = trim((string)($a['task_subject'] ?? ''));
      $taskDueDays = isset($a['task_due_days']) ? (int)$a['task_due_days'] : null;
      $taskAssignedTo = trim((string)($a['task_assigned_to'] ?? ''));
      $webhookUrl = trim((string)($a['webhook_url'] ?? ''));
      $webhookMethod = trim((string)($a['webhook_method'] ?? ''));

      $this->db->q("
        INSERT INTO limocrm_workflow_actions
        (id, workflow_id, action_type, email_template_id, delay_value, delay_unit, target_field, target_value,
         task_subject, task_due_days, task_assigned_to, webhook_url, webhook_method, sort_order, created_at, deleted)
        VALUES
        ('".$this->db->esc($aid)."', '$widQ', '".$this->db->esc($type)."',
         ".($emailTemplateId !== '' ? "'".$this->db->esc($emailTemplateId)."'" : "NULL").",
         ".($delayValue !== null ? (string)$delayValue : "NULL").",
         ".($delayUnit !== '' ? "'".$this->db->esc($delayUnit)."'" : "NULL").",
         ".($targetField !== '' ? "'".$this->db->esc($targetField)."'" : "NULL").",
         ".($targetValue !== '' ? "'".$this->db->esc($targetValue)."'" : "NULL").",
         ".($taskSubject !== '' ? "'".$this->db->esc($taskSubject)."'" : "NULL").",
         ".($taskDueDays !== null ? (string)$taskDueDays : "NULL").",
         ".($taskAssignedTo !== '' ? "'".$this->db->esc($taskAssignedTo)."'" : "NULL").",
         ".($webhookUrl !== '' ? "'".$this->db->esc($webhookUrl)."'" : "NULL").",
         ".($webhookMethod !== '' ? "'".$this->db->esc($webhookMethod)."'" : "NULL").",
         $j, '$now', 0)
      ");
    }

    return ['success' => true, 'workflow_id' => $wid];
  }
}


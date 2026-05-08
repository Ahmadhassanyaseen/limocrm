<?php
// Cron executor for LimoCRM workflow automation
// Run every minute: php G:/XAMPP/htdocs/limocrm/cron/workflow_executor.php

require_once __DIR__ . '/../config/database.php';

function guid() {
  // GUID-ish (sufficient for this system); prefer DB/Suite GUID when available
  $data = random_bytes(16);
  $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
  $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function dbq(mysqli $conn, string $v): string {
  return mysqli_real_escape_string($conn, $v);
}

function log_event(mysqli $conn, array $row) {
  $id = guid();
  $instance_id = $row['instance_id'] ?? null;
  $workflow_id = $row['workflow_id'] ?? null;
  $module_name = $row['module_name'] ?? null;
  $record_id = $row['record_id'] ?? null;
  $action_id = $row['action_id'] ?? null;
  $sort_order = isset($row['sort_order']) ? (int)$row['sort_order'] : null;
  $event_type = $row['event_type'] ?? 'action_ok';
  $message = $row['message'] ?? '';
  $payload_json = $row['payload_json'] ?? null;
  $created_at = $row['created_at'] ?? date('Y-m-d H:i:s');

  $instance_sql = $instance_id ? ("'".dbq($conn, $instance_id)."'") : "NULL";
  $workflow_sql = $workflow_id ? ("'".dbq($conn, $workflow_id)."'") : "NULL";
  $module_sql = $module_name ? ("'".dbq($conn, $module_name)."'") : "NULL";
  $record_sql = $record_id ? ("'".dbq($conn, $record_id)."'") : "NULL";
  $action_sql = $action_id ? ("'".dbq($conn, $action_id)."'") : "NULL";
  $sort_sql = ($sort_order === null) ? "NULL" : (string)$sort_order;
  $payload_sql = ($payload_json === null) ? "NULL" : ("'".dbq($conn, $payload_json)."'");

  $sql = "
    INSERT INTO limocrm_workflow_logs
    (id, instance_id, workflow_id, module_name, record_id, action_id, sort_order, event_type, message, payload_json, created_at)
    VALUES
    ('$id', $instance_sql, $workflow_sql, $module_sql, $record_sql, $action_sql, $sort_sql, '".dbq($conn, $event_type)."', '".dbq($conn, $message)."', $payload_sql, '".dbq($conn, $created_at)."')
  ";
  mysqli_query($conn, $sql);
}

function suite_send_email(string $module, string $record_id, string $email_template_id): array {
  $endpoint = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
  $ch = curl_init($endpoint);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'action' => 'send_workflow_email',
    'module_name' => $module,
    'record_id' => $record_id,
    'email_template_id' => $email_template_id,
  ]);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $resp = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch);

  if ($resp === false) {
    return ['success' => false, 'message' => $err ?: 'cURL error'];
  }
  $decoded = json_decode($resp, true);
  if (!is_array($decoded)) {
    return ['success' => false, 'message' => 'Non-JSON response', 'raw' => substr((string)$resp, 0, 800)];
  }
  return $decoded;
}

function parse_delay_to_seconds(int $value, string $unit): int {
  $u = strtolower(trim($unit));
  if ($u === 'minutes' || $u === 'minute') return $value * 60;
  if ($u === 'hours' || $u === 'hour') return $value * 3600;
  if ($u === 'days' || $u === 'day') return $value * 86400;
  return $value * 60;
}

function eval_condition($left, string $op, string $right): bool {
  $op = trim($op);
  if ($op === 'contains') return stripos((string)$left, (string)$right) !== false;
  if ($op === '=') return (string)$left === (string)$right;
  if ($op === '!=') return (string)$left !== (string)$right;

  // numeric comparisons if possible
  $ln = is_numeric($left) ? (float)$left : null;
  $rn = is_numeric($right) ? (float)$right : null;
  if ($ln !== null && $rn !== null) {
    if ($op === '>') return $ln > $rn;
    if ($op === '<') return $ln < $rn;
  }
  return false;
}

// Fetch runnable instances
$now = date('Y-m-d H:i:s');
$instancesSql = "
  SELECT *
  FROM limocrm_workflow_instances
  WHERE deleted=0
    AND status IN ('running','paused')
    AND (next_run_at IS NULL OR next_run_at <= '$now')
  ORDER BY next_run_at IS NULL DESC, next_run_at ASC
  LIMIT 50
";
$instancesRes = mysqli_query($conn, $instancesSql);
if (!$instancesRes) {
  fwrite(STDERR, "Failed to query instances\n");
  exit(1);
}

while ($inst = mysqli_fetch_assoc($instancesRes)) {
  $instanceId = $inst['id'];
  $workflowId = $inst['workflow_id'];
  $moduleName = $inst['module_name'];
  $recordId = $inst['record_id'];
  $current = (int)($inst['current_sort_order'] ?? 1);

  // Mark as running and bump updated_at (simple overlap protection)
  mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='running', updated_at='$now' WHERE id='".dbq($conn,$instanceId)."' AND deleted=0");

  // Process until delay or end (max steps to prevent infinite loops)
  $stepCount = 0;
  $maxSteps = 20;
  $halt = false;

  while (!$halt && $stepCount < $maxSteps) {
    $stepCount++;

    $actionRes = mysqli_query($conn, "
      SELECT *
      FROM limocrm_workflow_actions
      WHERE workflow_id='".dbq($conn, $workflowId)."'
        AND deleted=0
        AND sort_order=".$current."
      LIMIT 1
    ");
    $action = $actionRes ? mysqli_fetch_assoc($actionRes) : null;

    if (!$action) {
      // No more actions -> complete
      mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='completed', next_run_at=NULL, updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
      log_event($conn, [
        'instance_id' => $instanceId,
        'workflow_id' => $workflowId,
        'module_name' => $moduleName,
        'record_id' => $recordId,
        'sort_order' => $current,
        'event_type' => 'completed',
        'message' => 'Workflow completed',
      ]);
      break;
    }

    $type = $action['action_type'];
    $actionId = $action['id'];

    if ($type === 'send_email') {
      $tmpl = (string)($action['email_template_id'] ?? '');
      if ($tmpl === '') {
        mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='failed', last_error='Missing email_template_id', updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
        log_event($conn, [
          'instance_id' => $instanceId,
          'workflow_id' => $workflowId,
          'module_name' => $moduleName,
          'record_id' => $recordId,
          'action_id' => $actionId,
          'sort_order' => $current,
          'event_type' => 'action_failed',
          'message' => 'send_email missing email_template_id',
        ]);
        break;
      }

      $res = suite_send_email($moduleName, $recordId, $tmpl);
      if (!($res['success'] ?? false)) {
        $msg = (string)($res['message'] ?? 'Email send failed');
        mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='failed', last_error='".dbq($conn,$msg)."', updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
        log_event($conn, [
          'instance_id' => $instanceId,
          'workflow_id' => $workflowId,
          'module_name' => $moduleName,
          'record_id' => $recordId,
          'action_id' => $actionId,
          'sort_order' => $current,
          'event_type' => 'action_failed',
          'message' => $msg,
          'payload_json' => json_encode($res, JSON_UNESCAPED_SLASHES),
        ]);
        break;
      }

      log_event($conn, [
        'instance_id' => $instanceId,
        'workflow_id' => $workflowId,
        'module_name' => $moduleName,
        'record_id' => $recordId,
        'action_id' => $actionId,
        'sort_order' => $current,
        'event_type' => 'action_ok',
        'message' => 'Email sent',
        'payload_json' => json_encode($res, JSON_UNESCAPED_SLASHES),
      ]);

      $current++;
      mysqli_query($conn, "UPDATE limocrm_workflow_instances SET current_sort_order=$current, updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
      continue;
    }

    if ($type === 'delay') {
      $val = (int)($action['delay_value'] ?? 0);
      $unit = (string)($action['delay_unit'] ?? 'minutes');
      if ($val <= 0) $val = 1;
      $seconds = parse_delay_to_seconds($val, $unit);
      $next = date('Y-m-d H:i:s', time() + $seconds);

      mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='paused', next_run_at='".dbq($conn,$next)."', updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
      log_event($conn, [
        'instance_id' => $instanceId,
        'workflow_id' => $workflowId,
        'module_name' => $moduleName,
        'record_id' => $recordId,
        'action_id' => $actionId,
        'sort_order' => $current,
        'event_type' => 'action_ok',
        'message' => "Delayed $val $unit",
        'payload_json' => json_encode(['next_run_at' => $next], JSON_UNESCAPED_SLASHES),
      ]);

      $current++;
      mysqli_query($conn, "UPDATE limocrm_workflow_instances SET current_sort_order=$current WHERE id='".dbq($conn,$instanceId)."'");
      $halt = true;
      continue;
    }

    if ($type === 'condition') {
      $field = (string)($action['condition_field'] ?? '');
      $op = (string)($action['condition_operator'] ?? '=');
      $val = (string)($action['condition_value'] ?? '');

      if ($field === '') {
        log_event($conn, [
          'instance_id' => $instanceId,
          'workflow_id' => $workflowId,
          'module_name' => $moduleName,
          'record_id' => $recordId,
          'action_id' => $actionId,
          'sort_order' => $current,
          'event_type' => 'action_failed',
          'message' => 'Condition missing field',
        ]);
        mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='failed', last_error='Condition missing field', updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
        break;
      }

      // Best-effort field read from SuiteCRM DB tables:
      // Leads -> leads + leads_cstm (field may be in _cstm)
      $left = null;
      if ($moduleName === 'Leads') {
        $fieldQ = dbq($conn, $field);
        $ridQ = dbq($conn, $recordId);
        $q = mysqli_query($conn, "SELECT l.$fieldQ AS v FROM leads l WHERE l.id='$ridQ' AND l.deleted=0 LIMIT 1");
        if ($q && ($r = mysqli_fetch_assoc($q)) && array_key_exists('v', $r)) {
          $left = $r['v'];
        } else {
          // try cstm
          $q2 = mysqli_query($conn, "SELECT c.$fieldQ AS v FROM leads_cstm c WHERE c.id_c='$ridQ' LIMIT 1");
          if ($q2 && ($r2 = mysqli_fetch_assoc($q2)) && array_key_exists('v', $r2)) {
            $left = $r2['v'];
          }
        }
      }

      $ok = eval_condition($left, $op, $val);
      if ($ok) {
        log_event($conn, [
          'instance_id' => $instanceId,
          'workflow_id' => $workflowId,
          'module_name' => $moduleName,
          'record_id' => $recordId,
          'action_id' => $actionId,
          'sort_order' => $current,
          'event_type' => 'action_ok',
          'message' => 'Condition true',
          'payload_json' => json_encode(['field'=>$field,'operator'=>$op,'value'=>$val,'left'=>$left], JSON_UNESCAPED_SLASHES),
        ]);
        $current++;
        mysqli_query($conn, "UPDATE limocrm_workflow_instances SET current_sort_order=$current, updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
        continue;
      }

      // Condition false => stop workflow (completed)
      log_event($conn, [
        'instance_id' => $instanceId,
        'workflow_id' => $workflowId,
        'module_name' => $moduleName,
        'record_id' => $recordId,
        'action_id' => $actionId,
        'sort_order' => $current,
        'event_type' => 'completed',
        'message' => 'Condition false; workflow stopped',
        'payload_json' => json_encode(['field'=>$field,'operator'=>$op,'value'=>$val,'left'=>$left], JSON_UNESCAPED_SLASHES),
      ]);
      mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='completed', next_run_at=NULL, updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
      $halt = true;
      continue;
    }

    // Unknown action type
    mysqli_query($conn, "UPDATE limocrm_workflow_instances SET status='failed', last_error='Unknown action type', updated_at='$now' WHERE id='".dbq($conn,$instanceId)."'");
    log_event($conn, [
      'instance_id' => $instanceId,
      'workflow_id' => $workflowId,
      'module_name' => $moduleName,
      'record_id' => $recordId,
      'action_id' => $actionId,
      'sort_order' => $current,
      'event_type' => 'action_failed',
      'message' => 'Unknown action type: ' . $type,
    ]);
    break;
  }
}

echo "OK\n";


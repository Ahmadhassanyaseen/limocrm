<?php
require_once __DIR__ . '/Db.php';

class ConditionEvaluator {
  private Db $db;

  public function __construct(Db $db) {
    $this->db = $db;
  }

  /**
   * Group semantics:
   * - condition_group: AND within a group
   * - OR between groups
   */
  public function evaluate(string $workflowId, array $record): bool {
    $wf = $this->db->esc($workflowId);
    $conds = $this->db->all("
      SELECT condition_field, condition_operator, condition_value, condition_group, sort_order
      FROM limocrm_workflow_conditions
      WHERE workflow_id='$wf' AND deleted=0
      ORDER BY condition_group ASC, sort_order ASC
    ");

    if (!count($conds)) return false; // conditions are first-class; do not allow empty

    $groups = [];
    foreach ($conds as $c) {
      $g = (int)($c['condition_group'] ?? 1);
      if (!isset($groups[$g])) $groups[$g] = [];
      $groups[$g][] = $c;
    }

    foreach ($groups as $condsInGroup) {
      $allTrue = true;
      foreach ($condsInGroup as $c) {
        $field = (string)$c['condition_field'];
        $op = (string)$c['condition_operator'];
        $val = (string)($c['condition_value'] ?? '');

        $left = $this->getFieldValue($record, $field);
        if (!$this->match($left, $op, $val)) {
          $allTrue = false;
          break;
        }
      }

      if ($allTrue) return true; // OR between groups
    }

    return false;
  }

  private function getFieldValue(array $record, string $field) {
    // case-insensitive lookup
    foreach ($record as $k => $v) {
      if (strcasecmp((string)$k, $field) === 0) return $v;
    }
    return null;
  }

  private function match($left, string $op, string $right): bool {
    $op = strtolower(trim($op));
    $l = $left;
    $r = $right;

    if ($op === 'is_empty') return $l === null || trim((string)$l) === '';
    if ($op === 'is_not_empty') return !($l === null || trim((string)$l) === '');

    $ls = (string)$l;
    $rs = (string)$r;

    if ($op === 'equals') return $ls === $rs;
    if ($op === 'not_equals') return $ls !== $rs;
    if ($op === 'contains') return stripos($ls, $rs) !== false;
    if ($op === 'not_contains') return stripos($ls, $rs) === false;
    if ($op === 'starts_with') return $rs === '' ? true : strncasecmp($ls, $rs, strlen($rs)) === 0;
    if ($op === 'ends_with') {
      if ($rs === '') return true;
      return strcasecmp(substr($ls, -strlen($rs)), $rs) === 0;
    }

    $ln = is_numeric($ls) ? (float)$ls : null;
    $rn = is_numeric($rs) ? (float)$rs : null;
    if ($ln !== null && $rn !== null) {
      if ($op === 'greater_than') return $ln > $rn;
      if ($op === 'less_than') return $ln < $rn;
    }

    return false;
  }
}


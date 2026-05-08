-- CRM Workflow Automation Engine (Conditions-first) migration
-- Target DB: the same SuiteCRM DB used by config/database.php
-- Safe-ish approach: create new tables if missing; add columns where needed.
-- NOTE: This migration introduces a NEW execution table: limocrm_workflow_execution_log
-- and a NEW conditions table: limocrm_workflow_conditions.

-- 1) limocrm_workflows (definition)
CREATE TABLE IF NOT EXISTS limocrm_workflows (
  id VARCHAR(36) PRIMARY KEY,
  workflow_name VARCHAR(255) NOT NULL,
  module_name VARCHAR(100) NOT NULL,
  trigger_type ENUM('on_create','on_update','on_field_change','on_date','scheduled') NOT NULL,
  trigger_field VARCHAR(100) NULL,
  trigger_value VARCHAR(255) NULL,
  status ENUM('Active','Inactive') DEFAULT 'Active',
  description TEXT NULL,
  run_once TINYINT(1) DEFAULT 1,
  created_by VARCHAR(36),
  date_entered DATETIME,
  date_modified DATETIME NULL,
  deleted TINYINT(1) DEFAULT 0,
  KEY idx_wf_module_status_deleted (module_name, status, deleted),
  KEY idx_wf_trigger (trigger_type, trigger_field)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add missing columns to existing limocrm_workflows (if table pre-exists older version)
ALTER TABLE limocrm_workflows
  ADD COLUMN IF NOT EXISTS trigger_type ENUM('on_create','on_update','on_field_change','on_date','scheduled') NULL,
  ADD COLUMN IF NOT EXISTS trigger_field VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS trigger_value VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS run_once TINYINT(1) DEFAULT 1,
  ADD COLUMN IF NOT EXISTS date_modified DATETIME NULL;

-- 2) limocrm_workflow_conditions
CREATE TABLE IF NOT EXISTS limocrm_workflow_conditions (
  id VARCHAR(36) PRIMARY KEY,
  workflow_id VARCHAR(36) NOT NULL,
  condition_field VARCHAR(100) NOT NULL,
  condition_operator ENUM(
    'equals','not_equals','contains','not_contains',
    'greater_than','less_than','is_empty','is_not_empty',
    'starts_with','ends_with'
  ) NOT NULL,
  condition_value VARCHAR(255) NULL,
  condition_group INT DEFAULT 1,
  sort_order INT DEFAULT 0,
  deleted TINYINT(1) DEFAULT 0,
  KEY idx_wfc_wf_group_order (workflow_id, condition_group, sort_order),
  CONSTRAINT fk_wfc_workflow FOREIGN KEY (workflow_id) REFERENCES limocrm_workflows(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3) limocrm_workflow_actions (expanded action types)
CREATE TABLE IF NOT EXISTS limocrm_workflow_actions (
  id VARCHAR(36) PRIMARY KEY,
  workflow_id VARCHAR(36) NOT NULL,
  action_type ENUM(
    'send_email','delay','update_field','create_task',
    'change_status','send_notification','webhook'
  ) NOT NULL,
  email_template_id VARCHAR(36) NULL,
  delay_value INT NULL,
  delay_unit ENUM('minutes','hours','days','weeks') NULL,
  target_field VARCHAR(100) NULL,
  target_value VARCHAR(255) NULL,
  task_subject VARCHAR(255) NULL,
  task_due_days INT NULL,
  task_assigned_to VARCHAR(36) NULL,
  webhook_url TEXT NULL,
  webhook_method ENUM('GET','POST') NULL,
  sort_order INT DEFAULT 0,
  created_at DATETIME,
  deleted TINYINT(1) DEFAULT 0,
  KEY idx_wfa_wf_order (workflow_id, sort_order),
  CONSTRAINT fk_wfa_workflow FOREIGN KEY (workflow_id) REFERENCES limocrm_workflows(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- If you previously had limocrm_workflow_actions with different columns,
-- add the new ones (no-op if already present).
ALTER TABLE limocrm_workflow_actions
  ADD COLUMN IF NOT EXISTS target_field VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS target_value VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS task_subject VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS task_due_days INT NULL,
  ADD COLUMN IF NOT EXISTS task_assigned_to VARCHAR(36) NULL,
  ADD COLUMN IF NOT EXISTS webhook_url TEXT NULL,
  ADD COLUMN IF NOT EXISTS webhook_method ENUM('GET','POST') NULL,
  ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0;

-- 4) limocrm_workflow_execution_log (state machine per workflow+record)
CREATE TABLE IF NOT EXISTS limocrm_workflow_execution_log (
  id VARCHAR(36) PRIMARY KEY,
  workflow_id VARCHAR(36) NOT NULL,
  record_id VARCHAR(36) NOT NULL,
  module_name VARCHAR(100),
  current_action_index INT DEFAULT 0,
  status ENUM('pending','running','completed','failed','waiting') DEFAULT 'pending',
  next_run_at DATETIME NULL,
  last_error TEXT NULL,
  started_at DATETIME,
  completed_at DATETIME NULL,
  updated_at DATETIME NULL,
  deleted TINYINT(1) DEFAULT 0,
  KEY idx_wfel_wf_record (workflow_id, record_id),
  KEY idx_wfel_status_next (status, next_run_at),
  CONSTRAINT fk_wfel_workflow FOREIGN KEY (workflow_id) REFERENCES limocrm_workflows(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


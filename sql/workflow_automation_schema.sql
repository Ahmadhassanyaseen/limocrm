-- LimoCRM Workflow Automation Engine schema
-- Target DB: the same DB used by SuiteCRM in this environment.
-- Run these statements once.

-- 1) Workflow definitions (ensure table exists in desired shape)
-- If you already have limocrm_workflows, review/adjust columns accordingly.
CREATE TABLE IF NOT EXISTS limocrm_workflows (
  id CHAR(36) NOT NULL,
  workflow_name VARCHAR(255) DEFAULT NULL,
  module_name VARCHAR(100) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Active',
  description TEXT,
  created_by CHAR(36) DEFAULT NULL,
  date_entered DATETIME DEFAULT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_workflows_module_status_deleted (module_name, status, deleted),
  KEY idx_workflows_date_entered (date_entered)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2) Workflow actions (ordered steps)
CREATE TABLE IF NOT EXISTS limocrm_workflow_actions (
  id CHAR(36) NOT NULL,
  workflow_id CHAR(36) NOT NULL,
  action_type VARCHAR(50) NOT NULL, -- send_email, delay, condition, update_field
  email_template_id CHAR(36) DEFAULT NULL,
  delay_value INT DEFAULT NULL,
  delay_unit VARCHAR(10) DEFAULT NULL, -- minutes, hours, days
  condition_field VARCHAR(255) DEFAULT NULL,
  condition_operator VARCHAR(20) DEFAULT NULL, -- =, !=, >, <, contains
  condition_value VARCHAR(255) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_actions_workflow_order_deleted (workflow_id, sort_order, deleted),
  KEY idx_actions_type (action_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3) Workflow instances (per record execution state)
CREATE TABLE IF NOT EXISTS limocrm_workflow_instances (
  id CHAR(36) NOT NULL,
  workflow_id CHAR(36) NOT NULL,
  module_name VARCHAR(100) NOT NULL,
  record_id CHAR(36) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'running', -- running, paused, completed, failed, cancelled
  current_sort_order INT NOT NULL DEFAULT 1,
  next_run_at DATETIME DEFAULT NULL,
  last_error TEXT,
  created_at DATETIME DEFAULT NULL,
  updated_at DATETIME DEFAULT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_instances_workflow_record_deleted (workflow_id, record_id, deleted),
  KEY idx_instances_status_next_run (status, next_run_at),
  KEY idx_instances_module_record (module_name, record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4) Workflow logs (audit/debug)
CREATE TABLE IF NOT EXISTS limocrm_workflow_logs (
  id CHAR(36) NOT NULL,
  instance_id CHAR(36) DEFAULT NULL,
  workflow_id CHAR(36) DEFAULT NULL,
  module_name VARCHAR(100) DEFAULT NULL,
  record_id CHAR(36) DEFAULT NULL,
  action_id CHAR(36) DEFAULT NULL,
  sort_order INT DEFAULT NULL,
  event_type VARCHAR(40) NOT NULL, -- started, action_ok, action_skipped, action_failed, completed
  message TEXT,
  payload_json LONGTEXT,
  created_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_logs_instance_created (instance_id, created_at),
  KEY idx_logs_workflow_record_created (workflow_id, record_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


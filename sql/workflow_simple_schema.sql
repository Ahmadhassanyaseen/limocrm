-- Simple Workflow Automation (single condition + single email template)
-- Run on the same DB as SuiteCRM (zabrinxyz_suitecrm7)

-- Workflows: ONLY 4 main fields + meta
CREATE TABLE IF NOT EXISTS limocrm_workflows (
  id CHAR(36) NOT NULL,
  workflow_name VARCHAR(255) NOT NULL,
  module_name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_by CHAR(36) NULL,
  date_entered DATETIME NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_lw_module_status_deleted (module_name, status, deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Trigger/Condition + Template: 1 row per workflow
CREATE TABLE IF NOT EXISTS limocrm_workflow_triggers (
  id CHAR(36) NOT NULL,
  workflow_id CHAR(36) NOT NULL,
  condition_type ENUM('on_create','status_equals','event_date') NOT NULL,
  status_value VARCHAR(255) NULL,
  event_date_field VARCHAR(100) NULL,
  email_template_id CHAR(36) NOT NULL,
  created_at DATETIME NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_lwt_workflow (workflow_id),
  KEY idx_lwt_type (condition_type),
  KEY idx_lwt_template (email_template_id),
  CONSTRAINT fk_lwt_workflow FOREIGN KEY (workflow_id) REFERENCES limocrm_workflows(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Optional: if you had older multi-step tables, keep them or drop manually:
-- DROP TABLE limocrm_workflow_actions;
-- DROP TABLE limocrm_workflow_instances;
-- DROP TABLE limocrm_workflow_logs;


-- Widget branding per CRM user (SuiteCRM DB). Run once.

CREATE TABLE IF NOT EXISTS limo_widget_theme (
  id CHAR(36) NOT NULL,
  user_id CHAR(36) NOT NULL COMMENT 'SuiteCRM users.id',
  accent_color VARCHAR(16) NOT NULL DEFAULT '#6366f1',
  font_family VARCHAR(128) NOT NULL DEFAULT 'Inter',
  date_entered DATETIME NULL DEFAULT NULL,
  date_modified DATETIME NULL DEFAULT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_widget_theme_user (user_id),
  KEY idx_widget_theme_deleted (deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

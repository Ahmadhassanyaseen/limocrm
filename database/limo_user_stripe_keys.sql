-- Stores each CRM user's Stripe API keys (one row per user).
-- Run on the SuiteCRM database.

CREATE TABLE IF NOT EXISTS limo_user_stripe_keys (
  id CHAR(36) NOT NULL,
  user_id CHAR(36) NOT NULL COMMENT 'SuiteCRM users.id',
  stripe_publishable_key VARCHAR(512) NOT NULL DEFAULT '',
  stripe_secret_key VARCHAR(512) NOT NULL DEFAULT '',
  is_live TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = test mode, 1 = live mode',
  connected_at DATETIME NULL DEFAULT NULL,
  date_entered DATETIME NULL DEFAULT NULL,
  date_modified DATETIME NULL DEFAULT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_stripe_user (user_id),
  KEY idx_stripe_deleted (deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

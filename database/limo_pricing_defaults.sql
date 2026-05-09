-- Pricing defaults per fleet user (SuiteCRM / MariaDB / MySQL).
-- Run this against the same database as SuiteCRM (e.g. limogen).
-- After creating the table, deploy the matching handlers in custom/CustomEntryPoint.php
-- (fetch_pricing_defaults, save_pricing_defaults, update_vehicle_pricing) if not already present.

CREATE TABLE IF NOT EXISTS limo_pricing_defaults (
  id CHAR(36) NOT NULL,
  user_id CHAR(36) NOT NULL COMMENT 'SuiteCRM users.id — fleet owner',
  default_hourly_rate DECIMAL(16,2) NOT NULL DEFAULT 0.00 COMMENT 'Default $/hour for new quotes',
  fuel_surcharge_pct DECIMAL(8,4) NOT NULL DEFAULT 0.0000 COMMENT 'Percentage of quoted subtotal (matches vehicle fuel_c)',
  driver_commission_pct DECIMAL(8,4) NOT NULL DEFAULT 0.0000 COMMENT 'Percentage of quoted subtotal (matches vehicle driver_commission_c)',
  date_entered DATETIME DEFAULT NULL,
  date_modified DATETIME DEFAULT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_limo_pricing_user (user_id),
  KEY idx_limo_pricing_deleted (deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

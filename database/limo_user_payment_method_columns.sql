-- Add preferred checkout method + PayPal credentials (SuiteCRM DB).
-- Run once. If a column already exists, skip that ALTER or remove duplicate lines.

ALTER TABLE limo_user_stripe_keys
  ADD COLUMN preferred_payment VARCHAR(24) NOT NULL DEFAULT 'offline'
    COMMENT 'stripe | paypal | offline' AFTER stripe_secret_key;

ALTER TABLE limo_user_stripe_keys
  ADD COLUMN paypal_client_id VARCHAR(512) NOT NULL DEFAULT '' AFTER preferred_payment;

ALTER TABLE limo_user_stripe_keys
  ADD COLUMN paypal_secret VARCHAR(512) NOT NULL DEFAULT '' AFTER paypal_client_id;

ALTER TABLE limo_user_stripe_keys
  ADD COLUMN paypal_is_live TINYINT(1) NOT NULL DEFAULT 0 AFTER paypal_secret;

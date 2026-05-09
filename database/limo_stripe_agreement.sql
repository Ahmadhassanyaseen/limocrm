-- =====================================================================
-- Public agreement flow: Stripe payments + limo_stripe_transactions log
-- Run on your SuiteCRM database (same DB as leads / contacts).
-- =====================================================================

CREATE TABLE IF NOT EXISTS limo_stripe_transactions (
  id CHAR(36) NOT NULL,
  lead_id CHAR(36) NOT NULL,
  contact_id CHAR(36) NULL DEFAULT NULL,
  stripe_customer_id VARCHAR(255) NULL DEFAULT NULL,
  stripe_payment_intent_id VARCHAR(255) NULL DEFAULT NULL,
  amount_cents INT NOT NULL,
  currency VARCHAR(3) NOT NULL DEFAULT 'usd',
  status VARCHAR(64) NOT NULL DEFAULT 'pending',
  description VARCHAR(255) NULL DEFAULT NULL,
  signature_file VARCHAR(512) NULL DEFAULT NULL,
  date_entered DATETIME NULL DEFAULT NULL,
  raw_response MEDIUMTEXT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_limo_tx_lead (lead_id),
  KEY idx_limo_tx_pi (stripe_payment_intent_id),
  KEY idx_limo_tx_customer (stripe_customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: add Studio fields stripe_customer_id_c on Leads and Contacts (varchar 255),
-- OR run once (adjust if column already exists):

-- ALTER TABLE leads_cstm    ADD COLUMN stripe_customer_id_c VARCHAR(255) NULL DEFAULT NULL;
-- ALTER TABLE contacts_cstm ADD COLUMN stripe_customer_id_c VARCHAR(255) NULL DEFAULT NULL;

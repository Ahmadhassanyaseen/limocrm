-- SuiteCRM DB: outbound SMTP configs for Limo CRM (replacing outbound_email bean for this workflow).
-- Run once, then wire CustomEntryPoint to these functions (see custom_entry_point_limo_outbound_email_accounts.php).

CREATE TABLE IF NOT EXISTS limo_outbound_email_accounts (
    id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    account_type VARCHAR(16) NOT NULL DEFAULT 'system' COMMENT 'system | personal',
    created_by CHAR(36) NOT NULL COMMENT 'users.id who created the record in Limo CRM',
    assigned_user_id CHAR(36) NULL DEFAULT NULL COMMENT 'users.id for personal accounts; NULL for system',
    mail_smtpserver VARCHAR(255) NOT NULL DEFAULT '',
    mail_smtpport VARCHAR(8) NOT NULL DEFAULT '25',
    mail_smtpssl VARCHAR(8) NOT NULL DEFAULT '0',
    mail_smtpauth_req TINYINT(1) NOT NULL DEFAULT 0,
    mail_smtpuser VARCHAR(255) NOT NULL DEFAULT '',
    mail_smtppass LONGTEXT NULL,
    smtp_from_name VARCHAR(255) NOT NULL DEFAULT '',
    smtp_from_addr VARCHAR(255) NOT NULL DEFAULT '',
    reply_to_name VARCHAR(255) NOT NULL DEFAULT '',
    reply_to_addr VARCHAR(255) NOT NULL DEFAULT '',
    signature LONGTEXT NULL,
    mail_sendtype VARCHAR(20) NOT NULL DEFAULT 'SMTP',
    mail_smtptype VARCHAR(20) NOT NULL DEFAULT 'SMTP',
    date_entered DATETIME NULL,
    date_modified DATETIME NULL,
    deleted TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_limooe_created_by (created_by),
    KEY idx_limooe_assigned (assigned_user_id),
    KEY idx_limooe_deleted (deleted),
    KEY idx_limooe_type (account_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

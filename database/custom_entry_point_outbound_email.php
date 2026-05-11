<?php
/**
 * ============================================================
 *  DEPLOY ON SUITECRM: add to CustomEntryPoint.php (limogen)
 * ============================================================
 *
 * Prerequisites:
 * - Studio → Outbound Email Accounts → add TextField **owner_c** ("owner"),
 *   then Quick Repair & Rebuild.
 *
 * Add to the switch($action):
 *
 *    case 'fetch_outbound_email_accounts':
 *        echo json_encode(fetch_outbound_email_accounts($data));
 *        break;
 *    case 'fetch_outbound_email_account_detail':
 *        echo json_encode(fetch_outbound_email_account_detail($data));
 *        break;
 *    case 'save_outbound_email_account':
 *        echo json_encode(save_outbound_email_account($data));
 *        break;
 *    case 'delete_outbound_email_account':
 *        echo json_encode(delete_outbound_email_account($data));
 *        break;
 *    case 'test_outbound_email_account_connection':
 *        echo json_encode(test_outbound_email_account_connection($data));
 *        break;
 *
 * Paste the functions below into CustomEntryPoint.php (after includes).
 * ============================================================
 */

function limo_oe_resolve_owner(&$bean, string $fallbackUserId): string {
    if (!empty($bean->owner_c)) {
        return trim((string)$bean->owner_c);
    }
    if ($bean && !empty($bean->id)) {
        global $db;
        $idQ = "'" . $db->quote($bean->id) . "'";
        $r = $db->query("SELECT owner_c FROM outbound_email_cstm WHERE id_c = {$idQ} LIMIT 1");
        if ($r) {
            $row = $db->fetchByAssoc($r);
            if ($row && !empty(trim((string)($row['owner_c'] ?? '')))) {
                return trim((string)$row['owner_c']);
            }
        }
    }
    return $fallbackUserId;
}

function fetch_outbound_email_accounts($data) {
    global $db;
    $userId = trim((string)($data['user_id'] ?? ''));
    if ($userId === '') {
        return ['success' => false, 'message' => 'Missing user_id'];
    }

    $uidEsc = $db->quote($userId);
    $sql = "SELECT oe.id, oe.name, oe.type, oe.user_id, oe.mail_smtpserver, oe.mail_smtpport,
                   oe.mail_smtpssl, oe.mail_smtpauth_req, oe.smtp_from_name, oe.smtp_from_addr,
                   oe.date_modified, u.user_name
            FROM outbound_email oe
            INNER JOIN outbound_email_cstm oec ON oec.id_c = oe.id
            LEFT JOIN users u ON u.id = oe.user_id AND u.deleted = 0
            WHERE oe.deleted = 0 AND oec.owner_c = '{$uidEsc}'
            ORDER BY oe.name ASC";

    $res = $db->query($sql);
    $rows = [];
    if ($res) {
        while ($row = $db->fetchByAssoc($res)) {
            $type = strtolower((string)($row['type'] ?? ''));
            $un = trim((string)($row['user_name'] ?? ''));
            $row['assignee'] = ($type === 'system') ? 'System' : ($un !== '' ? $un : 'User');
            unset($row['user_name']);
            $rows[] = $row;
        }
    }

    return ['success' => true, 'accounts' => $rows];
}

function fetch_outbound_email_account_detail($data) {
    global $db;
    global $db;
    $userId = trim((string)($data['user_id'] ?? ''));
    $id = trim((string)($data['id'] ?? ''));
    if ($userId === '' || $id === '') {
        return ['success' => false, 'message' => 'Missing id or user_id'];
    }

    $bean = BeanFactory::getBean('OutboundEmailAccounts', $id);
    if (empty($bean->id)) {
        return ['success' => false, 'message' => 'Account not found'];
    }
    $owner = limo_oe_resolve_owner($bean, '');
    if ($owner !== $userId) {
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $idQ = "'" . $db->quote($id) . "'";
    $sql = "SELECT mail_smtppass FROM outbound_email WHERE id = {$idQ} AND deleted = 0 LIMIT 1";
    $hasPass = false;
    $pr = $db->query($sql);
    if ($pr) {
        $pw = $db->fetchByAssoc($pr);
        $hasPass = !empty(trim((string)($pw['mail_smtppass'] ?? '')));
    }

    $type = strtolower((string)($bean->type ?? 'user'));
    return [
        'success' => true,
        'account' => [
            'id' => $bean->id,
            'name' => (string)($bean->name ?? ''),
            'type' => $type,
            'user_id' => (string)($bean->user_id ?? ''),
            'smtp_from_name' => (string)($bean->smtp_from_name ?? ''),
            'smtp_from_addr' => (string)($bean->smtp_from_addr ?? ''),
            'reply_to_name' => (string)($bean->reply_to_name ?? ''),
            'reply_to_addr' => (string)($bean->reply_to_addr ?? ''),
            'signature' => (string)($bean->signature ?? ''),
            'mail_sendtype' => (string)($bean->mail_sendtype ?? 'SMTP'),
            'mail_smtptype' => (string)($bean->mail_smtptype ?? 'SMTP'),
            'mail_smtpserver' => (string)($bean->mail_smtpserver ?? ''),
            'mail_smtpport' => (string)($bean->mail_smtpport ?? '25'),
            'mail_smtpuser' => (string)($bean->mail_smtpuser ?? ''),
            'mail_smtpauth_req' => !empty($bean->mail_smtpauth_req) ? '1' : '0',
            'mail_smtpssl' => (string)($bean->mail_smtpssl ?? '0'),
            'mail_smtp_has_pass' => $hasPass,
        ],
    ];
}

function save_outbound_email_account($data) {
    global $db;
    $userId = trim((string)($data['user_id'] ?? ''));
    if ($userId === '') {
        return ['success' => false, 'message' => 'Missing user_id'];
    }

    $id = trim((string)($data['id'] ?? ''));
    if ($id !== '') {
        $bean = BeanFactory::getBean('OutboundEmailAccounts', $id);
        if (empty($bean->id)) {
            return ['success' => false, 'message' => 'Account not found'];
        }
        if (limo_oe_resolve_owner($bean, '') !== $userId) {
            return ['success' => false, 'message' => 'Forbidden'];
        }
    } else {
        $bean = BeanFactory::newBean('OutboundEmailAccounts');
    }

    $name = trim((string)($data['name'] ?? ''));
    if ($name === '' || mb_strlen($name) > 255) {
        return ['success' => false, 'message' => 'Account name is required (max 255 characters).'];
    }

    $scope = strtolower(trim((string)($data['account_scope'] ?? 'system')));
    if ($scope === 'system') {
        $bean->type = 'system';
        $bean->user_id = $userId;
    } else {
        $bean->type = 'user';
        $bean->user_id = $userId;
    }

    $server = trim((string)($data['mail_smtpserver'] ?? ''));
    if ($server === '' || !preg_match('/^[A-Za-z0-9.\-_\[\]:]+$/', $server)) {
        return ['success' => false, 'message' => 'SMTP server hostname is invalid.'];
    }
    $portRaw = trim((string)($data['mail_smtpport'] ?? ''));
    if (!preg_match('/^\d+$/', $portRaw)) {
        return ['success' => false, 'message' => 'SMTP port must be numeric.'];
    }
    $portNum = (int)$portRaw;
    if ($portNum < 1 || $portNum > 65535) {
        return ['success' => false, 'message' => 'SMTP port must be between 1 and 65535.'];
    }

    $smtpUser = trim((string)($data['mail_smtpuser'] ?? ''));
    if (mb_strlen($smtpUser) > 255) {
        return ['success' => false, 'message' => 'SMTP username is too long.'];
    }

    $smtpPass = isset($data['mail_smtppass']) ? (string)$data['mail_smtppass'] : '';

    $ssl = trim((string)($data['mail_smtpssl'] ?? '0'));
    if (!in_array($ssl, ['0', '1', '2'], true)) {
        $ssl = '0';
    }

    $authReq = isset($data['mail_smtpauth_req']) &&
        ($data['mail_smtpauth_req'] === '1' || $data['mail_smtpauth_req'] === 1 ||
            $data['mail_smtpauth_req'] === true || $data['mail_smtpauth_req'] === 'true');

    $bean->name = $name;
    $bean->smtp_from_name = substr(trim((string)($data['smtp_from_name'] ?? '')), 0, 255);
    $smtpFromAddr = trim((string)($data['smtp_from_addr'] ?? ''));
    if ($smtpFromAddr !== '' && !filter_var($smtpFromAddr, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'From address is not a valid email.'];
    }
    $bean->smtp_from_addr = $smtpFromAddr;

    $bean->reply_to_name = substr(trim((string)($data['reply_to_name'] ?? '')), 0, 255);
    $replyAddr = trim((string)($data['reply_to_addr'] ?? ''));
    if ($replyAddr !== '' && !filter_var($replyAddr, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Reply-To address is not a valid email.'];
    }
    $bean->reply_to_addr = $replyAddr;

    $sig = (string)($data['signature'] ?? '');
    if (mb_strlen($sig) > 65535) {
        return ['success' => false, 'message' => 'Signature is too long.'];
    }
    $bean->signature = $sig;

    $bean->mail_sendtype = substr(trim((string)($data['mail_sendtype'] ?? 'SMTP')), 0, 20);
    $bean->mail_smtptype = substr(trim((string)($data['mail_smtptype'] ?? 'SMTP')), 0, 20);
    $bean->mail_smtpserver = $server;
    $bean->mail_smtpport = (string)$portNum;
    $bean->mail_smtpuser = $smtpUser;

    $bean->mail_smtpauth_req = $authReq;
    $bean->mail_smtpssl = $ssl;

    // Password change: blank on edit retains stored password.
    if ($smtpPass !== '') {
        $bean->mail_smtppass = $smtpPass;
    } elseif ($id === '') {
        $bean->mail_smtppass = '';
    }

    if ($authReq && trim($smtpPass) === '' && $id === '') {
        return ['success' => false, 'message' => 'SMTP password is required when authentication is enabled for a new account.'];
    }

    $bean->owner_c = $userId;

    $bean->save();
    if (empty($bean->id)) {
        return ['success' => false, 'message' => 'Unable to save account.'];
    }

    return ['success' => true, 'message' => 'Saved', 'id' => $bean->id];
}

function delete_outbound_email_account($data) {
    $userId = trim((string)($data['user_id'] ?? ''));
    $id = trim((string)($data['id'] ?? ''));
    if ($userId === '' || $id === '') {
        return ['success' => false, 'message' => 'Missing id or user_id'];
    }

    $bean = BeanFactory::getBean('OutboundEmailAccounts', $id);
    if (empty($bean->id)) {
        return ['success' => false, 'message' => 'Account not found'];
    }
    if (limo_oe_resolve_owner($bean, '') !== $userId) {
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $bean->deleted = 1;
    $bean->save();

    return ['success' => true, 'message' => 'Deleted'];
}

function test_outbound_email_account_connection($data) {
    global $db;
    $userId = trim((string)($data['user_id'] ?? ''));
    $id = trim((string)($data['id'] ?? ''));
    if ($userId === '' || $id === '') {
        return ['success' => false, 'message' => 'Missing id or user_id'];
    }

    $bean = BeanFactory::getBean('OutboundEmailAccounts', $id);
    if (empty($bean->id)) {
        return ['success' => false, 'message' => 'Account not found'];
    }
    if (limo_oe_resolve_owner($bean, '') !== $userId) {
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $host = trim((string)($bean->mail_smtpserver ?? ''));
    $port = (int)($bean->mail_smtpport ?? 0);
    $ssl = (int)($bean->mail_smtpssl ?? 0);

    if ($host === '' || $port < 1 || $port > 65535) {
        return ['success' => false, 'message' => 'SMTP host or port is not configured'];
    }

    $scheme = 'tcp';
    if ($ssl === 1) {
        $scheme = 'ssl';
    } elseif ($ssl === 2) {
        $scheme = 'tls';
    }

    $remote = $scheme . '://' . $host . ':' . $port;

    set_error_handler(static function (): bool {
        return true;
    });
    $sock = stream_socket_client(
        $remote,
        $errno,
        $errstr,
        12,
        STREAM_CLIENT_CONNECT
    );
    restore_error_handler();

    if ($sock === false) {
        return [
            'success' => false,
            'message' => 'Cannot connect to ' . $host . ':' . $port .
                (($errstr !== '') ? (' — ' . $errstr . ' (' . $errno . ')') : ''),
        ];
    }

    fclose($sock);
    return ['success' => true, 'message' => 'TCP connection succeeded.'];
}

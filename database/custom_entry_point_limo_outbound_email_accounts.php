<?php
/**
 * ============================================================
 *  SUITECRM CustomEntryPoint — limo_outbound_email_accounts table
 * ============================================================
 *
 * Requires: database/limo_outbound_email_accounts.sql run on CRM DB.
 *
 * Use THE SAME LimoCRM API actions:
 *   fetch_outbound_email_accounts
 *   fetch_outbound_email_account_detail
 *   save_outbound_email_account
 *   delete_outbound_email_account
 *   test_outbound_email_account_connection
 *
 * Paste these functions into CustomEntryPoint.php (or require_once this file)
 * and REMOVE / replace any older outbound_email–bean implementations.
 *
 * Behaviour:
 *   - created_by: SuiteCRM users.id of the creator (Limo user_id posting save).
 *   - assigned_user_id: required for personal; NULL for system.
 *   - List: creator OR assignee sees the row.
 *   - Edit/delete: only creator.
 * ============================================================
 */

/** @var \DBManager $db */

function limoe_sql_quote($db, ?string $s): string
{
    return "'" . $db->quote((string)$s) . "'";
}

function limo_oe_normalize_account_type_from_request(array $data): string
{
    $explicit = strtolower(trim((string)($data['account_type_c'] ?? '')));
    if ($explicit !== '' && in_array($explicit, ['system', 'personal'], true)) {
        return $explicit;
    }

    $scope = strtolower(trim((string)($data['account_scope'] ?? 'system')));
    if (in_array($scope, ['personal', 'user'], true)) {
        return 'personal';
    }

    return 'system';
}

function limo_oe_user_exists(string $uid): bool
{
    $uid = trim($uid);
    if ($uid === '') {
        return false;
    }
    /** @var \User|false $u */
    $u = BeanFactory::getBean('Users', $uid);

    return !empty($u->id) && empty($u->deleted);
}

function limo_oe_row_access_read(array $row, string $userId): bool
{
    $userId = trim($userId);
    if ($userId === '') {
        return false;
    }

    return trim((string)($row['created_by'] ?? '')) === $userId
        || trim((string)($row['assigned_user_id'] ?? '')) === $userId;
}

function limo_oe_row_access_write(array $row, string $userId): bool
{
    return trim((string)$userId) !== ''
        && trim((string)($row['created_by'] ?? '')) === trim((string)$userId);
}

function limoe_new_uuid(): string
{
    if (function_exists('create_guid')) {
        return create_guid();
    }

    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        random_int(0, 0xffff),
        random_int(0, 0xffff),
        random_int(0, 0xffff),
        random_int(0, 0x0fff) | 0x4000,
        random_int(0, 0x3fff) | 0x8000,
        random_int(0, 0xffff),
        random_int(0, 0xffff),
        random_int(0, 0xffff)
    );
}

function fetch_outbound_email_accounts($data): array
{
    global $db;

    $userId = trim((string)($data['user_id'] ?? ''));
    if ($userId === '') {
        return ['success' => false, 'message' => 'Missing user_id'];
    }

    $qid = limoe_sql_quote($db, $userId);

    $sql = "SELECT a.id, a.name, a.account_type AS account_type_c, a.created_by, a.assigned_user_id,
                   a.mail_smtpserver, a.mail_smtpport, a.mail_smtpssl, a.mail_smtpauth_req,
                   a.smtp_from_name, a.smtp_from_addr, a.date_modified,
                   uc.user_name AS creator_user_name,
                   ua.user_name AS assigned_user_name
            FROM limo_outbound_email_accounts a
            LEFT JOIN users uc ON uc.id = a.created_by AND uc.deleted = 0
            LEFT JOIN users ua ON ua.id = a.assigned_user_id AND ua.deleted = 0
            WHERE a.deleted = 0
              AND (a.created_by = {$qid} OR a.assigned_user_id = {$qid})
            ORDER BY a.name ASC";

    $res = $db->query($sql);
    $rows = [];
    if ($res) {
        while ($row = $db->fetchByAssoc($res)) {
            $at = strtolower(trim((string)($row['account_type_c'] ?? '')));
            if ($at === '') {
                $at = 'system';
            }

            if ($at === 'system') {
                $label = 'System';
            } else {
                $an = trim((string)($row['assigned_user_name'] ?? ''));
                $label = $an !== '' ? $an : 'Personal';
            }
            $row['assignee'] = $label;

            unset($row['creator_user_name'], $row['assigned_user_name']);
            $rows[] = $row;
        }
    }

    return ['success' => true, 'accounts' => $rows];
}

function fetch_outbound_email_account_detail($data): array
{
    global $db;

    $userId = trim((string)($data['user_id'] ?? ''));
    $id = trim((string)($data['id'] ?? ''));
    if ($userId === '' || $id === '') {
        return ['success' => false, 'message' => 'Missing id or user_id'];
    }

    $idQ = limoe_sql_quote($db, $id);
    $sql = "SELECT * FROM limo_outbound_email_accounts WHERE id = {$idQ} AND deleted = 0 LIMIT 1";
    $res = $db->query($sql);
    if (!$res) {
        return ['success' => false, 'message' => 'Query failed'];
    }
    $row = $db->fetchByAssoc($res);
    if (!$row) {
        return ['success' => false, 'message' => 'Account not found'];
    }

    if (!limo_oe_row_access_read($row, $userId)) {
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $hasPass = !empty(trim((string)($row['mail_smtppass'] ?? '')));
    $at = strtolower(trim((string)($row['account_type'] ?? 'system')));

    return [
        'success' => true,
        'account' => [
            'id' => $row['id'],
            'name' => (string)($row['name'] ?? ''),
            'type' => $at === 'personal' ? 'user' : 'system',
            'account_type_c' => $at,
            'created_by' => (string)($row['created_by'] ?? ''),
            'assigned_user_id' => (string)($row['assigned_user_id'] ?? ''),
            'user_id' => (string)($row['created_by'] ?? ''),
            'smtp_from_name' => (string)($row['smtp_from_name'] ?? ''),
            'smtp_from_addr' => (string)($row['smtp_from_addr'] ?? ''),
            'reply_to_name' => (string)($row['reply_to_name'] ?? ''),
            'reply_to_addr' => (string)($row['reply_to_addr'] ?? ''),
            'signature' => (string)($row['signature'] ?? ''),
            'mail_sendtype' => (string)($row['mail_sendtype'] ?? 'SMTP'),
            'mail_smtptype' => (string)($row['mail_smtptype'] ?? 'SMTP'),
            'mail_smtpserver' => (string)($row['mail_smtpserver'] ?? ''),
            'mail_smtpport' => (string)($row['mail_smtpport'] ?? '25'),
            'mail_smtpuser' => (string)($row['mail_smtpuser'] ?? ''),
            'mail_smtpauth_req' => !empty($row['mail_smtpauth_req']) ? '1' : '0',
            'mail_smtpssl' => (string)($row['mail_smtpssl'] ?? '0'),
            'mail_smtp_has_pass' => $hasPass,
        ],
    ];
}

function save_outbound_email_account($data): array
{
    global $db;

    $userId = trim((string)($data['user_id'] ?? ''));
    if ($userId === '') {
        return ['success' => false, 'message' => 'Missing user_id'];
    }

    $accountType = limo_oe_normalize_account_type_from_request($data);

    $assignedUserId = trim((string)($data['assigned_user_id'] ?? ''));
    if ($accountType === 'system') {
        $assignedUserId = '';
    }
    if ($accountType === 'personal' && $assignedUserId === '') {
        return ['success' => false, 'message' => 'Personal accounts require an assigned user.'];
    }
    if ($assignedUserId !== '' && !limo_oe_user_exists($assignedUserId)) {
        return ['success' => false, 'message' => 'Assigned user does not exist.'];
    }

    $name = trim((string)($data['name'] ?? ''));

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

    $smtpFromName = substr(trim((string)($data['smtp_from_name'] ?? '')), 0, 255);
    $smtpFromAddr = trim((string)($data['smtp_from_addr'] ?? ''));
    if ($smtpFromAddr !== '' && !filter_var($smtpFromAddr, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'From address is not a valid email.'];
    }

    $replyName = substr(trim((string)($data['reply_to_name'] ?? '')), 0, 255);
    $replyAddr = trim((string)($data['reply_to_addr'] ?? ''));
    if ($replyAddr !== '' && !filter_var($replyAddr, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Reply-To address is not a valid email.'];
    }

    $sig = (string)($data['signature'] ?? '');
    if (mb_strlen($sig) > 65535) {
        return ['success' => false, 'message' => 'Signature is too long.'];
    }

    $sendType = substr(trim((string)($data['mail_sendtype'] ?? 'SMTP')), 0, 20);
    $smtpType = substr(trim((string)($data['mail_smtptype'] ?? 'SMTP')), 0, 20);

    $now = gmdate('Y-m-d H:i:s');
    $id = trim((string)($data['id'] ?? ''));

    $assignSql = ($assignedUserId === '') ? 'NULL' : limoe_sql_quote($db, $assignedUserId);
    $authInt = $authReq ? '1' : '0';

    if ($id !== '') {
        $idQ = limoe_sql_quote($db, $id);
        $res = $db->query("SELECT * FROM limo_outbound_email_accounts WHERE id = {$idQ} AND deleted = 0 LIMIT 1");
        $row = $res ? $db->fetchByAssoc($res) : null;
        if (!$row) {
            return ['success' => false, 'message' => 'Account not found'];
        }
        if (!limo_oe_row_access_write($row, $userId)) {
            return ['success' => false, 'message' => 'Forbidden (only creator can edit).'];
        }

        $passStored = trim((string)($row['mail_smtppass'] ?? ''));
        if ($authReq && $smtpPass === '' && $passStored === '') {
            return ['success' => false, 'message' => 'SMTP password is required when authentication is enabled.'];
        }

        if ($smtpPass !== '') {
            $passSql = limoe_sql_quote($db, $smtpPass);
        } else {
            $passSql = $passStored !== '' ? limoe_sql_quote($db, $passStored) : 'NULL';
        }

        $sqlUp =
            'UPDATE limo_outbound_email_accounts SET '
            . 'name=' . limoe_sql_quote($db, $name) . ', '
            . 'account_type=' . limoe_sql_quote($db, $accountType) . ', '
            . 'assigned_user_id=' . $assignSql . ', '
            . 'mail_smtpserver=' . limoe_sql_quote($db, $server) . ', '
            . 'mail_smtpport=' . limoe_sql_quote($db, (string)$portNum) . ', '
            . 'mail_smtpssl=' . limoe_sql_quote($db, $ssl) . ', '
            . 'mail_smtpauth_req=' . $authInt . ', '
            . 'mail_smtpuser=' . limoe_sql_quote($db, $smtpUser) . ', '
            . 'mail_smtppass=' . $passSql . ', '
            . 'smtp_from_name=' . limoe_sql_quote($db, $smtpFromName) . ', '
            . 'smtp_from_addr=' . limoe_sql_quote($db, $smtpFromAddr) . ', '
            . 'reply_to_name=' . limoe_sql_quote($db, $replyName) . ', '
            . 'reply_to_addr=' . limoe_sql_quote($db, $replyAddr) . ', '
            . 'signature=' . limoe_sql_quote($db, $sig) . ', '
            . 'mail_sendtype=' . limoe_sql_quote($db, $sendType) . ', '
            . 'mail_smtptype=' . limoe_sql_quote($db, $smtpType) . ', '
            . 'date_modified=' . limoe_sql_quote($db, $now) . ' '
            . 'WHERE id = ' . $idQ . ' AND deleted = 0 LIMIT 1';

        $ok = (bool)$db->query($sqlUp);
        if (!$ok) {
            return ['success' => false, 'message' => 'Save failed'];
        }

        return ['success' => true, 'message' => 'Saved', 'id' => $id];
    }

    /* Insert */
    if ($authReq && trim($smtpPass) === '') {
        return ['success' => false, 'message' => 'SMTP password is required when authentication is enabled for a new account.'];
    }

    $newId = limoe_new_uuid();
    $passSqlIns = trim($smtpPass) !== '' ? limoe_sql_quote($db, $smtpPass) : 'NULL';

    $cols = '`id`,`name`,`account_type`,`created_by`,`assigned_user_id`,'
        . '`mail_smtpserver`,`mail_smtpport`,`mail_smtpssl`,`mail_smtpauth_req`,'
        . '`mail_smtpuser`,`mail_smtppass`,`smtp_from_name`,`smtp_from_addr`,'
        . '`reply_to_name`,`reply_to_addr`,`signature`,`mail_sendtype`,`mail_smtptype`,`date_entered`,`date_modified`,`deleted`';

    $vals =
        limoe_sql_quote($db, $newId) . ',' .
        limoe_sql_quote($db, $name) . ',' .
        limoe_sql_quote($db, $accountType) . ',' .
        limoe_sql_quote($db, $userId) . ',' .
        $assignSql . ',' .
        limoe_sql_quote($db, $server) . ',' .
        limoe_sql_quote($db, (string)$portNum) . ',' .
        limoe_sql_quote($db, $ssl) . ',' .
        $authInt . ',' .
        limoe_sql_quote($db, $smtpUser) . ',' .
        $passSqlIns . ',' .
        limoe_sql_quote($db, $smtpFromName) . ',' .
        limoe_sql_quote($db, $smtpFromAddr) . ',' .
        limoe_sql_quote($db, $replyName) . ',' .
        limoe_sql_quote($db, $replyAddr) . ',' .
        limoe_sql_quote($db, $sig) . ',' .
        limoe_sql_quote($db, $sendType) . ',' .
        limoe_sql_quote($db, $smtpType) . ',' .
        limoe_sql_quote($db, $now) . ',' .
        limoe_sql_quote($db, $now) . ','
        . '0';

    $sqlIn = 'INSERT INTO limo_outbound_email_accounts (' . $cols . ') VALUES (' . $vals . ')';
    $ok = (bool)$db->query($sqlIn);

    return $ok
        ? ['success' => true, 'message' => 'Saved', 'id' => $newId]
        : ['success' => false, 'message' => 'Save failed'];
}

function delete_outbound_email_account($data): array
{
    global $db;

    $userId = trim((string)($data['user_id'] ?? ''));
    $id = trim((string)($data['id'] ?? ''));
    if ($userId === '' || $id === '') {
        return ['success' => false, 'message' => 'Missing id or user_id'];
    }

    $idQ = limoe_sql_quote($db, $id);
    $res = $db->query("SELECT * FROM limo_outbound_email_accounts WHERE id = {$idQ} AND deleted = 0 LIMIT 1");
    $row = $res ? $db->fetchByAssoc($res) : null;
    if (!$row) {
        return ['success' => false, 'message' => 'Account not found'];
    }
    if (!limo_oe_row_access_write($row, $userId)) {
        return ['success' => false, 'message' => 'Forbidden'];
    }

    $now = gmdate('Y-m-d H:i:s');
    $ok = (bool)$db->query(
        'UPDATE limo_outbound_email_accounts SET deleted=1, date_modified=' . limoe_sql_quote($db, $now)
            . ' WHERE id = ' . $idQ . ' LIMIT 1'
    );

    return $ok
        ? ['success' => true, 'message' => 'Deleted']
        : ['success' => false, 'message' => 'Delete failed'];
}

function test_outbound_email_account_connection($data): array
{
    $userId = trim((string)($data['user_id'] ?? ''));
    $id = trim((string)($data['id'] ?? ''));
    if ($userId === '' || $id === '') {
        return ['success' => false, 'message' => 'Missing id or user_id'];
    }

    $detail = fetch_outbound_email_account_detail([
        'user_id' => $userId,
        'id' => $id,
    ]);
    if (empty($detail['success'])) {
        return ['success' => false, 'message' => $detail['message'] ?? 'Unable to load account'];
    }
    $acct = $detail['account'];

    $host = trim((string)($acct['mail_smtpserver'] ?? ''));
    $port = (int)($acct['mail_smtpport'] ?? 0);
    $ssl = (int)($acct['mail_smtpssl'] ?? 0);

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

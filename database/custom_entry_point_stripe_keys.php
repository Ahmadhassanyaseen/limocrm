<?php
/**
 * ============================================================
 *  ADD TO CustomEntryPoint.php  —  Stripe Key Management
 * ============================================================
 *
 * 1) Add these cases to the main switch($action) block:
 *
 *    case 'fetch_user_stripe_keys':
 *        echo json_encode(fetch_user_stripe_keys($data));
 *        break;
 *
 *    case 'save_user_stripe_keys':
 *        echo json_encode(save_user_stripe_keys($data));
 *        break;
 *
 *    case 'delete_user_stripe_keys':
 *        echo json_encode(delete_user_stripe_keys($data));
 *        break;
 *
 *    case 'fetch_user_transactions':
 *        echo json_encode(fetch_user_transactions($data));
 *        break;
 *
 * 2) Paste the functions below anywhere in the file.
 * ============================================================
 */

function fetch_user_stripe_keys($data) {
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) return ['success' => false, 'message' => 'Missing user_id.'];

    $sql = "SELECT id, stripe_publishable_key, stripe_secret_key, is_live, connected_at
            FROM limo_user_stripe_keys
            WHERE user_id = '{$db->quote($userId)}' AND deleted = 0
            LIMIT 1";
    $row = $db->fetchOne($sql);

    if (!$row) {
        return ['success' => true, 'connected' => false, 'keys' => null];
    }

    return [
        'success'   => true,
        'connected' => true,
        'keys'      => [
            'id'                     => $row['id'],
            'stripe_publishable_key' => $row['stripe_publishable_key'],
            'stripe_secret_key'      => limo_mask_key($row['stripe_secret_key']),
            'is_live'                => (int) $row['is_live'],
            'connected_at'           => $row['connected_at'],
        ],
    ];
}

function limo_mask_key($key) {
    if (strlen($key) <= 12) return str_repeat('*', strlen($key));
    return substr($key, 0, 7) . str_repeat('*', strlen($key) - 11) . substr($key, -4);
}

function save_user_stripe_keys($data) {
    global $db;
    $userId  = trim($data['user_id'] ?? '');
    $pubKey  = trim($data['stripe_publishable_key'] ?? '');
    $secKey  = trim($data['stripe_secret_key'] ?? '');
    $isLive  = !empty($data['is_live']) ? 1 : 0;

    if (!$userId)  return ['success' => false, 'message' => 'Missing user_id.'];
    if (!$pubKey)  return ['success' => false, 'message' => 'Publishable key is required.'];
    if (!$secKey)  return ['success' => false, 'message' => 'Secret key is required.'];

    if (strpos($pubKey, 'pk_') !== 0) {
        return ['success' => false, 'message' => 'Publishable key must start with pk_'];
    }
    if (strpos($secKey, 'sk_') !== 0 && strpos($secKey, 'rk_') !== 0) {
        return ['success' => false, 'message' => 'Secret key must start with sk_ or rk_'];
    }

    $now = gmdate('Y-m-d H:i:s');
    $existing = $db->fetchOne(
        "SELECT id FROM limo_user_stripe_keys WHERE user_id = '{$db->quote($userId)}' AND deleted = 0 LIMIT 1"
    );

    if ($existing) {
        $db->query("UPDATE limo_user_stripe_keys
                     SET stripe_publishable_key = '{$db->quote($pubKey)}',
                         stripe_secret_key      = '{$db->quote($secKey)}',
                         is_live                = $isLive,
                         connected_at           = '$now',
                         date_modified           = '$now'
                     WHERE id = '{$existing['id']}'");
    } else {
        $id = create_guid();
        $db->query("INSERT INTO limo_user_stripe_keys
                     (id, user_id, stripe_publishable_key, stripe_secret_key, is_live, connected_at, date_entered, date_modified, deleted)
                     VALUES
                     ('$id', '{$db->quote($userId)}', '{$db->quote($pubKey)}', '{$db->quote($secKey)}', $isLive, '$now', '$now', '$now', 0)");
    }

    return ['success' => true, 'message' => 'Stripe keys saved successfully.'];
}

function delete_user_stripe_keys($data) {
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) return ['success' => false, 'message' => 'Missing user_id.'];

    $now = gmdate('Y-m-d H:i:s');
    $db->query("UPDATE limo_user_stripe_keys SET deleted = 1, date_modified = '$now'
                WHERE user_id = '{$db->quote($userId)}' AND deleted = 0");

    return ['success' => true, 'message' => 'Stripe connection removed.'];
}

function fetch_user_transactions($data) {
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) return ['success' => false, 'message' => 'Missing user_id.'];

    $sql = "SELECT t.id, t.lead_id, t.contact_id, t.stripe_customer_id,
                   t.stripe_payment_intent_id, t.amount_cents, t.status,
                   t.date_entered,
                   CONCAT(l.first_name, ' ', l.last_name) AS lead_name,
                   l.lead_source
            FROM limo_stripe_transactions t
            LEFT JOIN leads l ON l.id = t.lead_id
            WHERE l.created_by = '{$db->quote($userId)}'
              AND t.deleted = 0
            ORDER BY t.date_entered DESC";

    $result = $db->query($sql);
    $rows = [];
    while ($row = $db->fetchByAssoc($result)) {
        $row['amount'] = number_format(($row['amount_cents'] ?? 0) / 100, 2);
        $rows[] = $row;
    }

    return ['success' => true, 'data' => $rows];
}

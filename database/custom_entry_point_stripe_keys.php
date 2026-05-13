<?php
/**
 * ============================================================
 *  ADD TO CustomEntryPoint.php  —  Stripe + PayPal + payment preference
 * ============================================================
 *
 * 1) Add these cases to the main switch($action) block:
 *
 *    case 'fetch_user_stripe_keys':
 *        echo json_encode(fetch_user_stripe_keys($data));
 *        break;
 *
 *    case 'fetch_payment_methods':
 *        echo json_encode(fetch_payment_methods($data));
 *        break;
 *
 *    case 'save_user_payment_preference':
 *        echo json_encode(save_user_payment_preference($data));
 *        break;
 *
 *    case 'save_user_stripe_keys':
 *        echo json_encode(save_user_stripe_keys($data));
 *        break;
 *
 *    case 'save_user_paypal_keys':
 *        echo json_encode(save_user_paypal_keys($data));
 *        break;
 *
 *    case 'delete_user_stripe_keys':
 *        echo json_encode(delete_user_stripe_keys($data));
 *        break;
 *
 *    case 'delete_user_paypal_keys':
 *        echo json_encode(delete_user_paypal_keys($data));
 *        break;
 *
 *    case 'fetch_user_transactions':
 *        echo json_encode(fetch_user_transactions($data));
 *        break;
 *
 * 2) Run database/limo_user_payment_method_columns.sql on SuiteCRM DB (once).
 *
 * 3) Paste the functions below anywhere in the file.
 * ============================================================
 */

function limo_payment_pref_normalize($pref)
{
    $pref = strtolower(trim((string)$pref));
    if ($pref === 'stripe' || $pref === 'paypal' || $pref === 'offline') {
        return $pref;
    }
    return 'offline';
}

function limo_mask_key($key)
{
    if (strlen($key) <= 12) {
        return str_repeat('*', strlen($key));
    }
    return substr($key, 0, 7) . str_repeat('*', strlen($key) - 11) . substr($key, -4);
}

function fetch_user_stripe_keys($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }

    $sql = "SELECT id, stripe_publishable_key, stripe_secret_key, is_live, connected_at,
                   COALESCE(preferred_payment, 'offline') AS preferred_payment,
                   paypal_client_id, paypal_secret, COALESCE(paypal_is_live, 0) AS paypal_is_live
            FROM limo_user_stripe_keys
            WHERE user_id = '{$db->quote($userId)}' AND deleted = 0
            LIMIT 1";
    $row = $db->fetchOne($sql);

    if (!$row) {
        return [
            'success'               => true,
            'connected'             => false,
            'keys'                    => null,
            'preferred_payment'       => 'offline',
            'paypal_connected'        => false,
            'paypal'                  => null,
        ];
    }

    $hasStripe = trim((string)($row['stripe_publishable_key'] ?? '')) !== '' && trim((string)($row['stripe_secret_key'] ?? '')) !== '';
    $ppEmpty = trim((string)($row['paypal_client_id'] ?? '')) === '' && trim((string)($row['paypal_secret'] ?? '')) === '';

    return [
        'success'             => true,
        'connected'           => $hasStripe,
        'keys'                => [
            'id'                     => $row['id'],
            'stripe_publishable_key' => $row['stripe_publishable_key'],
            'stripe_secret_key'      => limo_mask_key($row['stripe_secret_key'] ?? ''),
            'is_live'                => (int) ($row['is_live'] ?? 0),
            'connected_at'           => $row['connected_at'],
        ],
        'preferred_payment'   => limo_payment_pref_normalize($row['preferred_payment'] ?? 'offline'),
        'paypal_connected'    => !$ppEmpty,
        'paypal'              => [
            'paypal_client_id' => $row['paypal_client_id'] ?? '',
            'paypal_secret'    => limo_mask_key($row['paypal_secret'] ?? ''),
            'paypal_is_live'     => (int) ($row['paypal_is_live'] ?? 0),
        ],
    ];
}

function save_user_payment_preference($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    $pref = limo_payment_pref_normalize($data['preferred_payment'] ?? 'offline');

    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }

    // Require credentials before switching to Stripe or PayPal
    if ($pref === 'stripe' || $pref === 'paypal') {
        $check = $db->fetchOne(
            "SELECT stripe_publishable_key, stripe_secret_key, paypal_client_id, paypal_secret
             FROM limo_user_stripe_keys
             WHERE user_id = '{$db->quote($userId)}' AND deleted = 0
             LIMIT 1"
        );
        if ($pref === 'stripe') {
            if (
                !$check
                || trim((string)($check['stripe_publishable_key'] ?? '')) === ''
                || trim((string)($check['stripe_secret_key'] ?? '')) === ''
            ) {
                return [
                    'success' => false,
                    'message' => 'Save your Stripe publishable and secret keys first, then select Stripe as the checkout method.',
                ];
            }
        }
        if ($pref === 'paypal') {
            if (
                !$check
                || trim((string)($check['paypal_client_id'] ?? '')) === ''
                || trim((string)($check['paypal_secret'] ?? '')) === ''
            ) {
                return [
                    'success' => false,
                    'message' => 'Save your PayPal Client ID and Secret first, then select PayPal as the checkout method.',
                ];
            }
        }
    }

    $now = gmdate('Y-m-d H:i:s');
    $existing = $db->fetchOne(
        "SELECT id FROM limo_user_stripe_keys WHERE user_id = '{$db->quote($userId)}' AND deleted = 0 LIMIT 1"
    );

    if ($existing) {
        $db->query("UPDATE limo_user_stripe_keys
                     SET preferred_payment = '{$db->quote($pref)}',
                         date_modified      = '$now'
                     WHERE id = '{$existing['id']}'");
    } else {
        $id = create_guid();
        $db->query("INSERT INTO limo_user_stripe_keys
                     (id, user_id, stripe_publishable_key, stripe_secret_key, preferred_payment,
                      paypal_client_id, paypal_secret, is_live, paypal_is_live,
                      connected_at, date_entered, date_modified, deleted)
                     VALUES
                     ('$id', '{$db->quote($userId)}', '', '', '{$db->quote($pref)}',
                      '', '', 0, 0,
                      NULL, '$now', '$now', 0)");
    }

    return ['success' => true, 'message' => 'Checkout method updated.', 'preferred_payment' => $pref    ];
}

/** Legacy / alias — return the same normalized payload as fetch_user_stripe_keys. */
function fetch_payment_methods($data)
{
    return fetch_user_stripe_keys($data);
}

function save_user_stripe_keys($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    $pubKey = trim($data['stripe_publishable_key'] ?? '');
    $secKey = trim($data['stripe_secret_key'] ?? '');
    $isLive = !empty($data['is_live']) ? 1 : 0;

    $prefExplicit = isset($data['preferred_payment'])
        ? limo_payment_pref_normalize($data['preferred_payment'])
        : 'stripe';

    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }
    if (!$pubKey) {
        return ['success' => false, 'message' => 'Publishable key is required.'];
    }
    if (!$secKey) {
        return ['success' => false, 'message' => 'Secret key is required.'];
    }

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
                         preferred_payment      = '{$db->quote($prefExplicit)}',
                         connected_at           = '$now',
                         date_modified          = '$now'
                     WHERE id = '{$existing['id']}'");
    } else {
        $id = create_guid();
        $db->query("INSERT INTO limo_user_stripe_keys
                     (id, user_id, stripe_publishable_key, stripe_secret_key, preferred_payment,
                      paypal_client_id, paypal_secret, is_live, paypal_is_live,
                      connected_at, date_entered, date_modified, deleted)
                     VALUES
                     ('$id', '{$db->quote($userId)}', '{$db->quote($pubKey)}', '{$db->quote($secKey)}', '{$db->quote($prefExplicit)}',
                      '', '', $isLive, 0,
                      '$now', '$now', '$now', 0)");
    }

    return ['success' => true, 'message' => 'Stripe keys saved successfully.'];
}

function save_user_paypal_keys($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    $clientId = trim($data['paypal_client_id'] ?? '');
    $secret = trim($data['paypal_secret'] ?? '');
    $isLive = !empty($data['paypal_is_live']) ? 1 : 0;
    $prefExplicit = isset($data['preferred_payment'])
        ? limo_payment_pref_normalize($data['preferred_payment'])
        : 'paypal';

    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }
    if (strlen($clientId) < 10) {
        return ['success' => false, 'message' => 'PayPal Client ID looks invalid.'];
    }
    if (strlen($secret) < 10) {
        return ['success' => false, 'message' => 'PayPal Secret is required.'];
    }

    $now = gmdate('Y-m-d H:i:s');
    $existing = $db->fetchOne(
        "SELECT id FROM limo_user_stripe_keys WHERE user_id = '{$db->quote($userId)}' AND deleted = 0 LIMIT 1"
    );

    if ($existing) {
        $db->query("UPDATE limo_user_stripe_keys
                     SET paypal_client_id = '{$db->quote($clientId)}',
                         paypal_secret    = '{$db->quote($secret)}',
                         paypal_is_live   = $isLive,
                         preferred_payment = '{$db->quote($prefExplicit)}',
                         date_modified    = '$now'
                     WHERE id = '{$existing['id']}'");
    } else {
        $id = create_guid();
        $db->query("INSERT INTO limo_user_stripe_keys
                     (id, user_id, stripe_publishable_key, stripe_secret_key, preferred_payment,
                      paypal_client_id, paypal_secret, is_live, paypal_is_live,
                      connected_at, date_entered, date_modified, deleted)
                     VALUES
                     ('$id', '{$db->quote($userId)}', '', '', '{$db->quote($prefExplicit)}',
                      '{$db->quote($clientId)}', '{$db->quote($secret)}', 0, $isLive,
                      NULL, '$now', '$now', 0)");
    }

    return ['success' => true, 'message' => 'PayPal credentials saved successfully.'];
}

function delete_user_stripe_keys($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }

    $now = gmdate('Y-m-d H:i:s');
    $db->query("UPDATE limo_user_stripe_keys
                SET stripe_publishable_key = '',
                    stripe_secret_key = '',
                    is_live = 0,
                    connected_at = NULL,
                    preferred_payment = CASE preferred_payment WHEN 'stripe' THEN 'offline' ELSE preferred_payment END,
                    date_modified = '$now'
                WHERE user_id = '{$db->quote($userId)}' AND deleted = 0");

    return ['success' => true, 'message' => 'Stripe connection removed.'];
}

function delete_user_paypal_keys($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }

    $now = gmdate('Y-m-d H:i:s');
    $db->query("UPDATE limo_user_stripe_keys
                SET paypal_client_id = '',
                    paypal_secret = '',
                    paypal_is_live = 0,
                    preferred_payment = CASE preferred_payment WHEN 'paypal' THEN 'offline' ELSE preferred_payment END,
                    date_modified = '$now'
                WHERE user_id = '{$db->quote($userId)}' AND deleted = 0");

    return ['success' => true, 'message' => 'PayPal credentials removed.'];
}

function fetch_user_transactions($data)
{
    global $db;
    $userId = trim($data['user_id'] ?? '');
    if (!$userId) {
        return ['success' => false, 'message' => 'Missing user_id.'];
    }

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

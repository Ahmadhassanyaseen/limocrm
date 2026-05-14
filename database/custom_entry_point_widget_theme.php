<?php
/**
 * ============================================================
 *  ADD TO CustomEntryPoint.php — widget accent + Google font family
 * ============================================================
 *
 * 1) Run database/limo_widget_theme.sql on the SuiteCRM database.
 *
 * 2) Add switch cases:
 *
 *    case 'fetch_widget_theme':
 *        echo json_encode(fetch_widget_theme($data));
 *        break;
 *
 *    case 'save_widget_theme':
 *        echo json_encode(save_widget_theme($data));
 *        break;
 *
 * 3) Paste the functions below into CustomEntryPoint.php.
 *
 * The hosted widget (widget.js) can read HTML data-accent-color /
 * data-font-family or call fetch_widget_theme with user_id to load styling.
 */

function limo_widget_theme_allowed_fonts()
{
    return [
        'Inter',
        'Roboto',
        'Open Sans',
        'Lato',
        'Montserrat',
        'Poppins',
        'Nunito',
        'Source Sans 3',
        'Raleway',
        'Ubuntu',
        'Oswald',
        'Rubik',
        'Work Sans',
        'DM Sans',
        'Merriweather',
        'Playfair Display',
        'Figtree',
        'Outfit',
        'Lexend',
    ];
}

function limo_widget_normalize_hex($color)
{
    $color = trim((string) $color);
    if ($color === '') {
        return '#6366f1';
    }
    $color = ltrim($color, '#');
    if (!preg_match('/^[0-9a-f]{6}$/i', $color)) {
        return null;
    }

    return '#' . strtolower($color);
}

function limo_widget_normalize_family($family)
{
    $family = trim((string) $family);
    $allowed = limo_widget_theme_allowed_fonts();

    foreach ($allowed as $a) {
        if (strcasecmp($family, $a) === 0) {
            return $a;
        }
    }

    return 'Inter';
}

function fetch_widget_theme($data)
{
    global $db;

    $userId = trim($data['user_id'] ?? '');
    if ($userId === '') {
        return ['success' => false, 'message' => 'Missing user_id'];
    }

    $defaults = [
        'success'        => true,
        'accent_color'   => '#6366f1',
        'font_family'    => 'Inter',
        'allowed_fonts'  => limo_widget_theme_allowed_fonts(),
    ];

    $sql = "SELECT accent_color, font_family
            FROM limo_widget_theme
            WHERE user_id = '{$db->quote($userId)}' AND deleted = 0
            LIMIT 1";
    $row = $db->fetchOne($sql);
    if (!$row) {
        return $defaults;
    }

    $hex = limo_widget_normalize_hex($row['accent_color'] ?? '');
    if ($hex === null) {
        $hex = '#6366f1';
    }

    return [
        'success'       => true,
        'accent_color'  => $hex,
        'font_family'   => limo_widget_normalize_family($row['font_family'] ?? 'Inter'),
        'allowed_fonts' => limo_widget_theme_allowed_fonts(),
    ];
}

function save_widget_theme($data)
{
    global $db;

    $userId = trim($data['user_id'] ?? '');
    if ($userId === '') {
        return ['success' => false, 'message' => 'Missing user_id'];
    }

    $hex = limo_widget_normalize_hex($data['accent_color'] ?? '');
    if ($hex === null) {
        return ['success' => false, 'message' => 'Invalid accent color (use #RRGGBB).'];
    }

    $family = limo_widget_normalize_family($data['font_family'] ?? 'Inter');
    $now = gmdate('Y-m-d H:i:s');

    $existing = $db->fetchOne(
        "SELECT id FROM limo_widget_theme WHERE user_id = '{$db->quote($userId)}' AND deleted = 0 LIMIT 1"
    );

    if ($existing && !empty($existing['id'])) {
        $db->query(
            "UPDATE limo_widget_theme SET
                accent_color = '{$db->quote($hex)}',
                font_family = '{$db->quote($family)}',
                date_modified = '$now'
             WHERE id = '{$existing['id']}'"
        );
    } else {
        $id = create_guid();
        $db->query(
            "INSERT INTO limo_widget_theme
             (id, user_id, accent_color, font_family, date_entered, date_modified, deleted)
             VALUES
             ('$id', '{$db->quote($userId)}', '{$db->quote($hex)}', '{$db->quote($family)}',
              '$now', '$now', 0)"
        );
    }

    return [
        'success'       => true,
        'message'       => 'Widget appearance saved.',
        'accent_color'  => $hex,
        'font_family'   => $family,
        'allowed_fonts' => limo_widget_theme_allowed_fonts(),
    ];
}

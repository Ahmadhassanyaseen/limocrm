<?php

// print_r($_POST);

$user_id = $_GET['user_id'] ?? '';
$source = $_GET['source'] ?? ($_POST['source'] ?? '');
if (empty($source) && !empty($_SERVER['HTTP_REFERER'])) {
    $refHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    if ($refHost) $source = $refHost;
}
$pickup = $_POST['pickup'] ?? '';
$destination = $_POST['destination'] ?? '';
$service_type = $_POST['service-type'] ?? '';
$service_length = $_POST['service_length'] ?? '5';
$passengers = $_POST['passengers'] ?? '20'; 
$pickup_date = $_POST['pickup-date'] ?? date('Y-m-d');
$postData = $_POST;
$postData['user_id'] = $user_id;
$vehicles = [];

$prodUrl = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';

if (isset($_POST) && !empty($_POST) && isset($postData) && !empty($postData)) {
    $postData['action'] = 'get_vehicles';
    $curl = curl_init($prodUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $response = curl_exec($curl);
    curl_close($curl);
    $vehicles = json_decode($response, true);
}

$wf_allowed_fonts = [
    'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins',
    'Nunito', 'Source Sans 3', 'Raleway', 'Ubuntu', 'Oswald', 'Rubik',
    'Work Sans', 'DM Sans', 'Merriweather', 'Playfair Display', 'Figtree', 'Outfit', 'Lexend',
];

function limo_wf_normalize_hex($hex)
{
    $hex = strtolower(ltrim(trim((string)$hex), '#'));
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return null;
    }
    return '#' . $hex;
}

function limo_wf_hex_to_rgb($hexWithHash)
{
    $hex = strtolower(ltrim(trim((string)$hexWithHash), '#'));
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return null;
    }

    return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
}

function limo_wf_rgb_to_hex(array $rgb)
{
    return sprintf('#%02x%02x%02x',
        max(0, min(255, (int)$rgb[0])),
        max(0, min(255, (int)$rgb[1])),
        max(0, min(255, (int)$rgb[2])));
}

function limo_wf_mix_rgb(array $rgb, array $towardRgb, float $t)
{
    return [
        (int)round($rgb[0] * (1 - $t) + $towardRgb[0] * $t),
        (int)round($rgb[1] * (1 - $t) + $towardRgb[1] * $t),
        (int)round($rgb[2] * (1 - $t) + $towardRgb[2] * $t),
    ];
}

function limo_wf_font_allowed($name, array $allowed)
{
    $t = trim((string)$name);
    if ($t === '') {
        return null;
    }
    foreach ($allowed as $f) {
        if (strcasecmp($f, $t) === 0) {
            return $f;
        }
    }
    return null;
}

function limo_wf_fetch_theme($prodUrl, $userId)
{
    $userId = trim((string)$userId);
    if ($userId === '') {
        return null;
    }
    $ch = curl_init($prodUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'action' => 'fetch_widget_theme',
        'user_id' => $userId,
    ]));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    $body = curl_exec($ch);
    curl_close($ch);
    $j = json_decode($body, true);

    return is_array($j) ? $j : null;
}

$wf_accent = limo_wf_normalize_hex(isset($_GET['accent_color']) ? $_GET['accent_color'] : '');
$wf_font = limo_wf_font_allowed(isset($_GET['font_family']) ? $_GET['font_family'] : '', $wf_allowed_fonts);

if ($user_id !== '' && ($wf_accent === null || $wf_font === null)) {
    $crmTheme = limo_wf_fetch_theme($prodUrl, $user_id);
    if (is_array($crmTheme) && !empty($crmTheme['success'])) {
        if ($wf_accent === null) {
            $hx = limo_wf_normalize_hex($crmTheme['accent_color'] ?? '');
            if ($hx !== null) {
                $wf_accent = $hx;
            }
        }
        if ($wf_font === null) {
            $fn = limo_wf_font_allowed($crmTheme['font_family'] ?? '', $wf_allowed_fonts);
            if ($fn !== null) {
                $wf_font = $fn;
            }
        }
    }
}

if ($wf_accent === null) {
    $wf_accent = '#ec268f';
}
if ($wf_font === null) {
    $wf_font = 'Inter';
}

$wf_rgb = limo_wf_hex_to_rgb($wf_accent);
if ($wf_rgb === null) {
    $wf_rgb = [236, 38, 143];
}

$wf_primary_light_hex = limo_wf_rgb_to_hex(limo_wf_mix_rgb($wf_rgb, [255, 255, 255], 0.38));
$wf_primary_dark_hex = limo_wf_rgb_to_hex(limo_wf_mix_rgb($wf_rgb, [0, 0, 0], 0.28));

$wf_font_link_family = preg_replace('/[^a-zA-Z0-9 \-]/', '', $wf_font);
if ($wf_font_link_family === '') {
    $wf_font_link_family = 'Inter';
}
$wf_google_family_qs = str_replace(' ', '+', $wf_font_link_family);
$wf_google_font_url = 'https://fonts.googleapis.com/css2?family=' . $wf_google_family_qs . ':wght@300;400;500;600;700;800&display=swap';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limogen - Premium Chauffeur Booking</title>
    <script>
        window.LIMO_WIDGET_THEME = <?php echo json_encode(['primary' => $wf_accent], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo htmlspecialchars($wf_google_font_url, ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: <?php echo htmlspecialchars($wf_accent, ENT_QUOTES, 'UTF-8'); ?>;
            --primary-light: <?php echo htmlspecialchars($wf_primary_light_hex, ENT_QUOTES, 'UTF-8'); ?>;
            --primary-dark: <?php echo htmlspecialchars($wf_primary_dark_hex, ENT_QUOTES, 'UTF-8'); ?>;
            --primary-rgb: <?php echo (int)$wf_rgb[0]; ?>, <?php echo (int)$wf_rgb[1]; ?>, <?php echo (int)$wf_rgb[2]; ?>;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1);
            --radius-sm: 8px;
            --radius: 14px;
            --radius-lg: 20px;
            --font-main: '<?php echo htmlspecialchars($wf_font, ENT_QUOTES, 'UTF-8'); ?>', sans-serif;
            --font-heading: '<?php echo htmlspecialchars($wf_font, ENT_QUOTES, 'UTF-8'); ?>', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: var(--font-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Hero & Form Section */
        .hero {
            max-width: 1280px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            padding: 0 24px;
        }

        .hero-left {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 32px;
            border: 1px solid var(--border);
        }

        .hero-left h1 {
            font-family: var(--font-heading);
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 24px;
            color: var(--text-main);
            line-height: 1.1;
        }

        .hero-left h1 span {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }
        /*#limogen-widget{*/
        /* height:800px;   */
        /*}*/

        .hero-left h1 span::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--primary);
            opacity: 0.15;
            z-index: -1;
        }

        .booking-form .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .field input, .field select, .field textarea {
            width: 100%;
            padding: 12px 16px;
            background: #f1f5f9;
            border: 1px solid transparent;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .field textarea {
            resize: vertical;
            min-height: 84px;
            line-height: 1.5;
        }

        .field input:focus, .field select:focus, .field textarea:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(var(--primary-rgb), 0.39);
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.23);
        }

        .btn:active {
            transform: translateY(0);
        }

        .hero-right {
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            height: 100%;
            min-height:400px;
        }

        /* Vehicle Grid Section */
        .vehicle_grid {
            max-width: 1280px;
            margin: 60px auto;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            padding: 0 24px;
        }

        .vehicle_sidebar {
            background: var(--surface);
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            height: max-content;
            border: 1px solid var(--border);
            position: sticky;
            top: 24px;
        }

        .vehicle_sidebar h4 {
            font-family: var(--font-heading);
            color: var(--text-main);
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vehicle_sidebar h4::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--primary);
            border-radius: 2px;
        }

        .check-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .check-group:hover {
            background: #f1f5f9;
        }

        .check-group input {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
        }

        /* ============================================================
           Vehicle cards — modern, user-centric layout
           ============================================================ */
        .cars {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        border-color 0.35s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
            border-color: var(--primary-light);
        }

        .card-media {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fafc, #e2e8f0);
        }

        .card-media-main {
            position: relative;
            aspect-ratio: 16 / 10;
            overflow: hidden;
        }

        .card-media-hero {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover .card-media-hero {
            transform: scale(1.06);
        }

        .card-media-thumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 8px 10px 10px;
            background: rgba(248, 250, 252, 0.95);
            border-top: 1px solid var(--border);
        }

        .card-media-thumb {
            flex: 0 0 auto;
            width: 52px;
            height: 40px;
            padding: 0;
            border: 2px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            background: #e2e8f0;
            transition: border-color 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease;
            opacity: 0.82;
        }

        .card-media-thumb:hover {
            opacity: 1;
            border-color: rgba(var(--primary-rgb), 0.45);
        }

        .card-media-thumb.is-active {
            opacity: 1;
            border-color: var(--primary);
            box-shadow: 0 0 0 1px rgba(var(--primary-rgb), 0.2);
        }

        .card-media-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            pointer-events: none;
        }

        .card-media-thumb:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .card-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: var(--primary);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-radius: 999px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.10);
        }

        .card-body {
            padding: 22px 24px 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            flex: 1;
        }

        .card-title {
            font-family: var(--font-heading);
            color: var(--text-main);
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.25;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-stats {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 0;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: var(--radius);
        }

        .card-stats li {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .card-stats i {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(var(--primary-rgb), 0.10);
            color: var(--primary);
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 36px;
        }

        .card-stats span {
            display: block;
            font-size: 0.66rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            line-height: 1;
            margin-bottom: 4px;
        }

        .card-stats strong {
            display: block;
            font-size: 0.9rem;
            color: var(--text-main);
            font-weight: 700;
            line-height: 1.2;
        }

        .card-features {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0;
            padding: 0;
        }

        .card-features li {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: rgba(34, 197, 94, 0.10);
            color: #15803d;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .card-features li i {
            font-size: 14px;
            line-height: 1;
        }

        .card-features li.more {
            background: #f1f5f9;
            color: var(--text-muted);
        }

        .card-cta {
            margin-top: auto;
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 14px 20px;
            border-radius: var(--radius);
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 16px rgba(var(--primary-rgb), 0.25);
        }

        .card-cta:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(var(--primary-rgb), 0.33);
        }

        .card-cta i {
            transition: transform 0.3s ease;
        }

        .card-cta:hover i {
            transform: translateX(4px);
        }

        .limocrm {
            width: 100%;
            /* max-width: 906px; */
            margin: auto;
        }

        /* Modal Content */
        .popup-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .popup-content {
            background: var(--surface);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            position: relative;
            animation: modalIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: var(--text-main);
        }

        .popup-content h3 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 32px;
            text-align: center;
        }

        .popup-content .field {
            margin-bottom: 20px;
        }

        /* Responsive Improvements */
        @media (max-width: 1024px) {
            .hero {
                grid-template-columns: 1fr;
            }
            .hero-right {
                height: 350px;
            }
            .vehicle_grid {
                grid-template-columns: 1fr;
            }
            .vehicle_sidebar {
                position: relative;
                top: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                padding-bottom: 32px;
            }
            .vehicle_sidebar h4 {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .booking-form .row {
                grid-template-columns: 1fr;
            }
            .hero-left {
                padding: 24px;
            }
            .hero-left h1 {
                font-size: 1.75rem;
            }
            .price-row {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            .round {
                width: 100%;
                justify-content: center;
            }
        }

        /* ===== Leaflet map + Nominatim autocomplete ===== */
        #route-map {
            width: 100%;
            height: 100%;
            min-height: 400px;
        }
        .lg-ac-wrap { position: relative; }
        .lg-ac-list {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0; right: 0;
            background: #fff;
            border-radius: var(--radius);
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.16);
            border: 1px solid var(--border);
            z-index: 50;
            overflow: hidden;
            max-height: 280px;
            overflow-y: auto;
        }
        .lg-ac-row {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }
        .lg-ac-row:last-child { border-bottom: none; }
        .lg-ac-row:hover, .lg-ac-row.active { background: #fdf2f8; }
        .lg-ac-row i { color: var(--primary); margin-top: 2px; }
        .lg-ac-primary {
            font-weight: 600;
            font-size: 0.92rem;
            color: var(--text-main);
        }
        .lg-ac-secondary {
            font-size: 0.78rem;
            color: var(--text-muted);
        }
        .lg-ac-loading,
        .lg-ac-empty {
            padding: 12px 14px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .lg-pin {
            background: transparent !important;
            border: none !important;
        }
        .lg-pin .lg-pin-dot {
            width: 18px;
            height: 18px;
            background: var(--c, var(--primary));
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }
        .card-slider {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16 / 10;
    background: #f1f5f9;
}

.card-slider-track {
    display: flex;
    height: 100%;
    transition: transform 0.35s ease;
}

.card-slider-img {
    width: 100%;
    flex: 0 0 100%;
    object-fit: cover;
}

/* arrows */
.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.9);
    border: none;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;

    opacity: 0;
    transition: all 0.25s ease;
    box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    z-index: 5;
}

.slider-btn i {
    font-size: 22px;
    color: #1e293b;
}

/* show arrows only on hover */
.card-slider:hover .slider-btn {
    opacity: 1;
}

.slider-btn.prev { left: 10px; }
.slider-btn.next { right: 10px; }

/* tag stays above */
.card-tag {
    z-index: 6;
}
    </style>
</head>
<body>

<div class="limocrm">
      <div class="hero">
        <div class="hero-left">
           
            <form class="booking-form" id="booking-form" action="?user_id=<?php echo urlencode($user_id); ?>&source=<?php echo urlencode($source); ?>" method="post">
              
                
                <div class="field">
                    <label>Pickup Location</label>
                    <input type="text" id="pickup" name="pickup" placeholder="Enter pickup address" required value="<?php echo $pickup; ?>">
                </div>

                <div class="field" style="margin-top: 20px;">
                    <label>Dropoff Location</label>
                    <input type="text" id="destination" name="destination" placeholder="Enter destination address" required value="<?php echo $destination; ?>">
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="field">
                        <label>Service Type</label>
                        <select name="service-type" id="service_type" required>
                            <option value="">Select service</option>
                        <?php 
                        $services = ["Airport", "Bachelor Party", "Bachelorette Party", "Birthday", "Casino", "Church Function", "Concert", "Construction Shuttle", "Convention", "Corporate Event", "Cruise Transfers", "Family Reunion", "General Day Trip", "Golf Outing", "Homecoming", "Night out on Town", "Over the Road", "Prom", "School Trip", "Shuttle Service", "Sports Event", "Theme Park", "Transfer", "Wedding", "Wedding Wire", "Wine Tour"];
                        foreach($services as $s) {
                            $selected = ($service_type == $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $selected>$s</option>";
                        }
                        ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Passengers</label>
                        <input type="number" id="passengers" name="passengers" min="1" max="100" value="<?php echo $passengers; ?>" required />
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="field">
                        <label>Pickup Date</label>
                        <input type="date" id="pickup-date" name="pickup-date" required value="<?php echo $pickup_date; ?>" />
                    </div>
                    <div class="field">
                        <label>Service Length (Hours)</label>
                        <input type="number" id="serviceLength" name="service_length" min="1" max="24" required value="<?php echo $service_length; ?>" />
                    </div>
                </div>

                <button type="submit" class="btn" style="margin-top: 20px;">Search Vehicles</button>
            </form>
            <div id="responseMsg" style="margin-top: 10px"></div>
        </div>
        <div class="hero-right">
            <div id="route-map"
                 data-pickup="<?php echo htmlspecialchars($pickup, ENT_QUOTES); ?>"
                 data-destination="<?php echo htmlspecialchars($destination, ENT_QUOTES); ?>"></div>
        </div>
    </div>
</div>
  
<script>

    const LIMO_PRIMARY_COLOR = (window.LIMO_WIDGET_THEME && window.LIMO_WIDGET_THEME.primary)
        ? window.LIMO_WIDGET_THEME.primary
        : '#ec268f';

    const today = new Date().toISOString().split("T")[0];

                                        // Get the input element
                                        const pickupDateInput = document.getElementById("pickup-date");

                                        // Set default and minimum date to today
                                        pickupDateInput.value = today;
                                        pickupDateInput.min = today;

    /* =====================================================================
       Free maps + autocomplete:
         - Map tiles:    OpenStreetMap (via Leaflet)
         - Autocomplete: Nominatim    (https://nominatim.openstreetmap.org)
         - Routing:      OSRM demo    (https://router.project-osrm.org)
       No API key required. Nominatim limits requests to ~1/sec; the
       autocomplete debounces input and only queries after 3+ chars.
       ===================================================================== */
    (function () {
        const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
        const OSRM_URL      = 'https://router.project-osrm.org/route/v1/driving';
        const DEFAULT_VIEW  = [26.1224, -80.1373]; // Broward County, FL fallback

        const mapEl = document.getElementById('route-map');
        const map = L.map(mapEl, {
            zoomControl: true,
            scrollWheelZoom: false,
        }).setView(DEFAULT_VIEW, 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let pickupMarker = null, destMarker = null, routeLine = null;
        const state = { pickup: null, dest: null };

        function pinIcon(color) {
            return L.divIcon({
                className: 'lg-pin',
                html: '<div class="lg-pin-dot" style="--c:' + color + '"></div>',
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });
        }

        function setMarker(which, latlng, label) {
            const color = which === 'pickup' ? '#22c55e' : LIMO_PRIMARY_COLOR;
            const icon  = pinIcon(color);
            if (which === 'pickup') {
                pickupMarker = pickupMarker
                    ? pickupMarker.setLatLng(latlng)
                    : L.marker(latlng, { icon, title: label || 'Pickup' }).addTo(map);
            } else {
                destMarker = destMarker
                    ? destMarker.setLatLng(latlng)
                    : L.marker(latlng, { icon, title: label || 'Destination' }).addTo(map);
            }
            state[which] = latlng;
            redrawRoute();
        }

        async function redrawRoute() {
            if (routeLine) { map.removeLayer(routeLine); routeLine = null; }
            const a = state.pickup, b = state.dest;
            if (!a && !b) return;
            if (a && !b) { map.setView(a, 13); return; }
            if (b && !a) { map.setView(b, 13); return; }

            // Try OSRM for an actual driving route; fall back to a dashed straight line.
            const url = `${OSRM_URL}/${a[1]},${a[0]};${b[1]},${b[0]}?overview=full&geometries=geojson`;
            try {
                const res  = await fetch(url);
                const data = await res.json();
                if (data && data.routes && data.routes[0]) {
                    const coords = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
                    routeLine = L.polyline(coords, { color: LIMO_PRIMARY_COLOR, weight: 5, opacity: 0.9 }).addTo(map);
                    map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
                    return;
                }
            } catch (_) { /* fall through */ }

            routeLine = L.polyline([a, b], {
                color: LIMO_PRIMARY_COLOR, weight: 4, opacity: 0.65, dashArray: '6 8'
            }).addTo(map);
            map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
        }

        async function nominatim(q, limit) {
            const url = `${NOMINATIM_URL}?format=json&countrycodes=us&addressdetails=1&limit=${limit}&q=${encodeURIComponent(q)}`;
            const res = await fetch(url, { headers: { 'Accept-Language': 'en' } });
            return res.json();
        }

        function attachAutocomplete(input, key) {
            const wrap = document.createElement('div');
            wrap.className = 'lg-ac-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);
            const list = document.createElement('div');
            list.className = 'lg-ac-list';
            wrap.appendChild(list);

            let timer = null, lastQuery = '', activeIdx = -1, items = [];

            function renderEmpty(message) {
                list.innerHTML = '<div class="lg-ac-empty">' + message + '</div>';
                list.style.display = 'block';
            }

            function renderItems(rs) {
                items = rs || [];
                activeIdx = -1;
                if (!items.length) { renderEmpty('No matches found.'); return; }
                list.innerHTML = '';
                items.forEach((it, i) => {
                    const a = it.address || {};
                    const primary   = it.name || a.city || a.town || a.village || a.hamlet
                                       || a.county || (it.display_name || '').split(',')[0];
                    const secondary = [a.city || a.town || a.village, a.state, a.postcode]
                                       .filter(Boolean).join(', ');
                    const row = document.createElement('div');
                    row.className = 'lg-ac-row';
                    row.dataset.idx = i;
                    row.innerHTML =
                        '<i class="ri-map-pin-line"></i>' +
                        '<div>' +
                            '<div class="lg-ac-primary"></div>' +
                            '<div class="lg-ac-secondary"></div>' +
                        '</div>';
                    row.querySelector('.lg-ac-primary').textContent   = primary;
                    row.querySelector('.lg-ac-secondary').textContent = secondary;
                    row.addEventListener('mousedown', e => {
                        e.preventDefault();
                        select(i);
                    });
                    list.appendChild(row);
                });
                list.style.display = 'block';
            }

            function select(i) {
                const it = items[i];
                if (!it) return;
                const a   = it.address || {};
                const lat = parseFloat(it.lat);
                const lon = parseFloat(it.lon);
                const cityish = a.city || a.town || a.village;
                const isZip = /^\d{5}(-\d{4})?$/.test(input.value.trim());
                if (isZip && cityish && a.state) {
                    input.value = `${a.postcode || input.value}, ${cityish}, ${a.state}`;
                } else {
                    input.value = it.display_name;
                }
                list.innerHTML = '';
                list.style.display = 'none';
                setMarker(key, [lat, lon], it.name || it.display_name);
            }

            async function search(q) {
                if (q.length < 3) { list.style.display = 'none'; return; }
                renderEmpty('Searching…');
                try {
                    const rs = await nominatim(q, 5);
                    if (input.value.trim() !== q) return; // outdated response
                    renderItems(rs);
                } catch (_) {
                    renderEmpty('Could not load suggestions.');
                }
            }

            input.addEventListener('input', () => {
                const q = input.value.trim();
                if (q === lastQuery) return;
                lastQuery = q;
                clearTimeout(timer);
                timer = setTimeout(() => search(q), 350);
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeIdx >= 0) select(activeIdx);
                    return;
                }
                if (!items.length || list.style.display === 'none') return;
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIdx = e.key === 'ArrowDown'
                        ? Math.min(items.length - 1, activeIdx + 1)
                        : Math.max(0, activeIdx - 1);
                    [...list.children].forEach((row, i) => {
                        row.classList.toggle('active', i === activeIdx);
                    });
                } else if (e.key === 'Escape') {
                    list.style.display = 'none';
                }
            });

            document.addEventListener('click', e => {
                if (!wrap.contains(e.target)) list.style.display = 'none';
            });
        }

        async function preGeocode(input, key) {
            const q = (input.value || '').trim();
            if (!q) return;
            try {
                const rs = await nominatim(q, 1);
                if (rs && rs[0]) {
                    setMarker(key, [parseFloat(rs[0].lat), parseFloat(rs[0].lon)],
                              rs[0].name || rs[0].display_name);
                }
            } catch (_) { /* silent */ }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const pickup      = document.getElementById('pickup');
            const destination = document.getElementById('destination');

            attachAutocomplete(pickup,      'pickup');
            attachAutocomplete(destination, 'dest');

            // Pre-geocode any values posted from the previous request so the
            // map immediately reflects the current pickup/destination.
            preGeocode(pickup,      'pickup');
            preGeocode(destination, 'dest');

            // Recompute size in case the widget was hidden when initialized.
            setTimeout(() => map.invalidateSize(), 200);
        });
    })();
                            </script>
      <script>
            document.getElementById('booking-form').addEventListener('submit', function(e) {
                                            e.preventDefault();  // Still prevent default initially for validation

                                            // Get form values
                                            let formError = document.getElementById('formError');
                                            const pickup = document.getElementById('pickup').value;
                                            const destination = document.getElementById('destination').value;
                                            const pickupDate = document.getElementById('pickup-date').value;
                                            const serviceType = document.getElementById('service_type').value;
                                            const passengers = document.getElementById('passengers').value;

                                            // Validation (unchanged)
                                            if (pickup === '' || destination === '' || pickupDate === '' || serviceType === '' || passengers === '') {
                                                formError.textContent = 'Please fill in all fields';
                                                return;
                                            }
                                           

                                            // If validation passes, submit the form (this will POST data and redirect to quote.php)
                                            this.submit();
                                        });
                                
      </script>

     
    </section>

<?php if(isset($vehicles) && !empty($vehicles)){ ?>

                    <div class="vehicle_grid" id="vehicle-results">
                        <div class="vehicle_sidebar">
                            <h4>Filter Vehicles</h4>
                            <?php 
                            $categories = ["Sedan", "SUV", "Mini Bus", "Stretch Limo", "Stretch SUV Limo", "Motor Coach"];
                            foreach($categories as $cat) {
                                ?>
                                <label class="check-group">
                                    <input type="checkbox" class="vehicle-filter" value="<?php echo $cat; ?>" /> <?php echo $cat; ?>
                                </label>
                                <?php
                            }
                            ?>
                        </div>
                        <div>
                            <div class="cars">
                                <?php
                                
                            
                                foreach($vehicles as $vehicle){
                                    $name      = trim((string)($vehicle['name'] ?? ''));
                                    $rawCat    = (string)($vehicle['vehicle_cetagory'] ?? '');
                                    $category  = trim(str_replace('_', ' ', $rawCat));
                                    $passenger = (int)($vehicle['passenger'] ?? 0);
                                    $bags      = (int)($vehicle['bags'] ?? 0);

                                    $fallback_vehicle_img = "https://zabrin.xyz/limogen/index.php?entryPoint=vehicle_image&id=" . urlencode($vehicle['id'] ?? '') . "&type=vehicle_xl";
                                    $raw_vehicle_images = trim((string)($vehicle['image_c'] ?? ''));
                                    if ($raw_vehicle_images === '' && !empty($vehicle['images_c'])) {
                                        $raw_vehicle_images = trim((string)$vehicle['images_c']);
                                    } 

                                    // print_r($raw_vehicle_images);
                                    $vehicle_image_urls = [];
                                    if ($raw_vehicle_images !== '') {
                                        foreach (explode(',', $raw_vehicle_images) as $_u) {
                                            $_u = trim((string)$_u);
                                            if ($_u !== '') {
                                                $vehicle_image_urls[] = $_u;
                                            }
                                        }
                                    }
                                    if (empty($vehicle_image_urls)) {
                                        $vehicle_image_urls[] = $fallback_vehicle_img;
                                    }
                                    // print_r($vehicle_image_urls);
                                    $hero_img_src = $vehicle_image_urls[0];
                                    $placeholder_img = 'https://via.placeholder.com/640x420?text=No+Image+Available';

                                    $facilitiesClean = [];
                                    if (!empty($vehicle['facilities'])) {
                                        foreach (explode(',', $vehicle['facilities']) as $f) {
                                            $f = trim(str_replace(['^', '_'], ['', ' '], $f));
                                            if ($f !== '') $facilitiesClean[] = $f;
                                        }
                                    }
                                    $featuredFacilities = array_slice($facilitiesClean, 0, 4);
                                    $extraCount = max(0, count($facilitiesClean) - count($featuredFacilities));
                                    ?>
                                    <article class="card"
                                             data-category="<?php echo htmlspecialchars($category, ENT_QUOTES); ?>"
                                             data-id="<?php echo htmlspecialchars($vehicle['id'] ?? '', ENT_QUOTES); ?>">
                                     <figure class="card-media">

    <div class="card-slider" data-slider>
        <div class="card-slider-track">
            <?php foreach ($vehicle_image_urls as $img): ?>
                <img class="card-slider-img"
                     src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars($name ?: 'Vehicle image', ENT_QUOTES); ?>">
            <?php endforeach; ?>
        </div>

        <!-- LEFT ARROW -->
        <button type="button" class="slider-btn prev" data-prev>
            <i class="ri-arrow-left-s-line"></i>
        </button>

        <!-- RIGHT ARROW -->
        <button type="button" class="slider-btn next" data-next>
            <i class="ri-arrow-right-s-line"></i>
        </button>

        <?php if ($category !== ''): ?>
            <span class="card-tag"><?php echo htmlspecialchars($category); ?></span>
        <?php endif; ?>
    </div>

</figure>

                                        <div class="card-body">
                                            <header>
                                                <h3 class="card-title"><?php echo htmlspecialchars($name ?: 'Untitled vehicle'); ?></h3>
                                            </header>

                                            <ul class="card-stats">
                                                <li>
                                                    <i class="ri-user-3-line"></i>
                                                    <div>
                                                        <span>Passengers</span>
                                                        <strong>Up to <?php echo $passenger; ?></strong>
                                                    </div>
                                                </li>
                                                <li>
                                                    <i class="ri-suitcase-line"></i>
                                                    <div>
                                                        <span>Luggage</span>
                                                        <strong>Up to <?php echo $bags; ?> <?php echo $bags === 1 ? 'bag' : 'bags'; ?></strong>
                                                    </div>
                                                </li>
                                            </ul>

                                            <?php if (!empty($featuredFacilities)): ?>
                                                <ul class="card-features">
                                                    <?php foreach ($featuredFacilities as $f): ?>
                                                        <li>
                                                            <i class="ri-check-line"></i>
                                                            <?php echo htmlspecialchars($f); ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                    <?php if ($extraCount > 0): ?>
                                                        <li class="more">+<?php echo $extraCount; ?> more</li>
                                                    <?php endif; ?>
                                                </ul>
                                            <?php endif; ?>

                                            <button type="button"
                                                    class="card-cta"
                                                    onclick='openQuoteModal(<?php echo json_encode($vehicle, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG); ?>)'>
                                                <span>Request Free Quote</span>
                                                <i class="ri-arrow-right-line"></i>
                                            </button>
                                        </div>
                                    </article>
                                    <?php
                                }
                                ?>
                            

                           
                            </div>

                            
                        </div>
                    </div>

                    <script>
                        // Smooth-scroll to the results when the page loads with vehicles.
                        document.addEventListener('DOMContentLoaded', function () {
                            const target = document.getElementById('vehicle-results');
                            if (!target) return;
                            // Small delay so the map / fonts have a chance to lay out first.
                            setTimeout(function () {
                                try {
                                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                } catch (_) {
                                    target.scrollIntoView();
                                }
                                // If the widget is inside an iframe, also scroll the host page.
                                if (window.parent && window.parent !== window) {
                                    try {
                                        const rect = target.getBoundingClientRect();
                                        window.parent.postMessage({
                                            type: 'limogen:scrollToResults',
                                            offsetTop: rect.top + window.scrollY
                                        }, '*');
                                    } catch (_) { /* cross-origin; ignore */ }
                                }
                            }, 250);
                        });
                    </script>

                    <?php } ?>

                    <!-- Add this after the vehicle grid, e.g., around line 3250 -->
<div id="quotePopup" class="popup-modal">
    <div class="popup-content">
        <i class="ri-close-line close-btn"></i>
        <h3>Get Quote for <br><span id="popupVehicleName" style="color: var(--primary);"></span></h3>
        <form id="quoteForm">
            <div class="field">
                <label>First Name</label>
                <input type="text" name="first_name" required placeholder="Your first name">
            </div>
            
            <div class="field">
                <label>Last Name</label>
                <input type="text" name="last_name" required placeholder="Your last name">
            </div>
            
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Your email">
            </div>
            
            <div class="field">
                <label>Phone Number</label>
                <input type="tel" name="phone" required placeholder="Your phone number">
            </div>

            <div class="field">
                <label>Description / Special Requests</label>
                <textarea name="notes" id="popup-notes" rows="3"
                          placeholder="Flight info, gate codes, child seats, special requests..."></textarea>
            </div>

            <input type="hidden" name="pickup" value="<?php echo $pickup ?>" />
            <input type="hidden" name="destination" value="<?php echo $destination ?>" />
            <input type="hidden" name="service_type" value="<?php echo $service_type ?>" />
            <input type="hidden" name="passengers" value="<?php echo $passengers ?>" />
            <input type="hidden" name="pickup_date" value="<?php echo $pickup_date ?>" />
            <input type="hidden" name="service_length" value="<?php echo $service_length ?>" />
            <input type="hidden" name="assigned_user_id" value="<?php echo $user_id ?>" />
            <input type="hidden" name="lead_source" value="<?php echo htmlspecialchars($source, ENT_QUOTES); ?>" />
            
            <button type="submit" class="btn" style="margin-top: 10px;">Submit Quote Request</button>
        </form>
        <div id="popupMessage"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.vehicle-filter');
        const cards = document.querySelectorAll('.card');

        // document.querySelectorAll('.card-media-thumbs').forEach(function (thumbRow) {
        //     const media = thumbRow.closest('.card-media');
        //     if (!media) return;
        //     const hero = media.querySelector('.card-media-hero');
        //     if (!hero) return;
        //     thumbRow.querySelectorAll('.card-media-thumb').forEach(function (btn) {
        //         btn.addEventListener('click', function () {
        //             const src = btn.getAttribute('data-src');
        //             if (!src) return;
        //             hero.src = src;
        //             thumbRow.querySelectorAll('.card-media-thumb').forEach(function (b) {
        //                 b.classList.remove('is-active');
        //                 b.setAttribute('aria-selected', 'false');
        //             });
        //             btn.classList.add('is-active');
        //             btn.setAttribute('aria-selected', 'true');
        //         });
        //     });
        // });
        document.querySelectorAll('.card-media-thumbs').forEach(function (thumbRow) {
    const media = thumbRow.closest('.card-media');
    if (!media) return;

    const hero = media.querySelector('.card-media-hero');
    if (!hero) return;

    thumbRow.querySelectorAll('.card-media-thumb').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const src = btn.getAttribute('data-src');
            if (!src) return;

            // swap main image
            hero.src = src;

            // active state
            thumbRow.querySelectorAll('.card-media-thumb').forEach(function (b) {
                b.classList.remove('is-active');
            });

            btn.classList.add('is-active');
        });
    });
});

        function filterVehicles() {
            const selectedCategories = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value.toLowerCase());

            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category').toLowerCase();
                if (selectedCategories.length === 0 || selectedCategories.includes(cardCategory)) {
                    card.style.display = 'block';  // Show matching or all if none selected
                } else {
                    card.style.display = 'none';  // Hide non-matching
                }
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', filterVehicles);
        });
    });
</script>

<script>
    let currentVehicle = {};

    function openQuoteModal(vehicle) {
        currentVehicle = vehicle;
        document.getElementById('quotePopup').style.display = 'flex';
        document.getElementById('popupVehicleName').textContent = vehicle.name;
        document.getElementById('popupMessage').textContent = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const popup = document.getElementById('quotePopup');
        const closeBtn = document.querySelector('.close-btn');
        const quoteForm = document.getElementById('quoteForm');

        // Close popup
        closeBtn.addEventListener('click', () => popup.style.display = 'none');
        window.addEventListener('click', (e) => { if (e.target === popup) popup.style.display = 'none'; });

        // Handle form submission and AJAX
        quoteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            const formData = new FormData(this);
            const pickup = document.getElementById('pickup') ? document.getElementById('pickup').value : '';
            const destination = document.getElementById('destination') ? document.getElementById('destination').value : '';
            const pickupDate = document.getElementById('pickup-date') ? document.getElementById('pickup-date').value : '';
            const serviceType = document.getElementById('service_type') ? document.getElementById('service_type').value : '';
            if (formData.set) {
                formData.set('pickup', pickup);
                formData.set('destination', destination);
                formData.set('pickup_date', pickupDate);
                formData.set('service_type', serviceType);
            } else {
                formData.append('pickup', pickup);
                formData.append('destination', destination);
                formData.append('pickup_date', pickupDate);
                formData.append('service_type', serviceType);
            }

            var currentSource = formData.get('lead_source') || '';
            if (!currentSource) {
                try {
                    var refHost = document.referrer ? new URL(document.referrer).hostname : '';
                    if (refHost) {
                        if (formData.set) { formData.set('lead_source', refHost); }
                        else { formData.append('lead_source', refHost); }
                    }
                } catch(e) {}
            }

            formData.append('vehicle_id', currentVehicle.id);
            formData.append('action', 'save_lead');

            fetch('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Quote request submitted!',
                        html: 'Thanks — we\'ve received your details.<br><br>'
                            + '<strong>Please check your email</strong> for your '
                            + '<strong>live trip rates</strong>. They usually arrive within a few minutes.',
                        confirmButtonText: 'Got it',
                        confirmButtonColor: LIMO_PRIMARY_COLOR,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Reset everything before navigating away.
                        quoteForm.reset();
                        popup.style.display = 'none';

                        const bookingForm = document.getElementById('booking-form');
                        if (bookingForm) bookingForm.reset();

                        // Force a clean reload (GET) so POST data is wiped and the
                        // browser doesn't show a "Confirm form resubmission" prompt.
                        try {
                            const url = new URL(window.location.href);
                            const keep = ['user_id', 'accent_color', 'font_family', 'theme', 'source'];
                            const parts = [];
                            keep.forEach(function (k) {
                                const v = url.searchParams.get(k);
                                if (v) parts.push(k + '=' + encodeURIComponent(v));
                            });
                            const cleanUrl = url.pathname + (parts.length ? '?' + parts.join('&') : '');
                            window.location.replace(cleanUrl);
                        } catch (_) {
                            window.location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Submission failed');
                }
            })
            .catch(error => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                console.error(error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Error submitting request. Please try again.',
                    icon: 'error',
                    confirmButtonColor: LIMO_PRIMARY_COLOR
                });
            });
        });
    });
    document.querySelectorAll('[data-slider]').forEach(slider => {
    const track = slider.querySelector('.card-slider-track');
    const slides = slider.querySelectorAll('.card-slider-img');
    const prev = slider.querySelector('[data-prev]');
    const next = slider.querySelector('[data-next]');

    let index = 0;

    function update() {
        track.style.transform = `translateX(-${index * 100}%)`;
    }

    next.addEventListener('click', (e) => {
        e.stopPropagation();
        index = (index + 1) % slides.length;
        update();
    });

    prev.addEventListener('click', (e) => {
        e.stopPropagation();
        index = (index - 1 + slides.length) % slides.length;
        update();
    });

    // optional: reset on hover out
    slider.addEventListener('mouseleave', () => {
        index = 0;
        update();
    });
});
</script>

    
</body>
</html>
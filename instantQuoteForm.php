<?php
declare(strict_types=1);

/**
 * Embed page for Limogen widget. Logs GET params name, email, website_url (each optional; log when any is present).
 *
 * Usage: instantQuoteForm.php?website_url=https://example.com&name=ahmad&email=a.com
 *
 * Embeds sanitized values into data-* on #limogen-widget.
 */

/**
 * Website URL from query — safe subset of URL characters for logging/embed (max length 2048).
 */
function instant_quote_sanitize_website_url(string $raw): string
{
    $t = strip_tags(trim($raw));
    $t = str_replace(["\r", "\n", "\0", "\t"], '', $t);
    if ($t === '') {
        return '';
    }
    if (strlen($t) > 2048) {
        $t = substr($t, 0, 2048);
    }

    return preg_replace('/[^a-zA-Z0-9._~:\/?#\[\]@!\$&\'()*+,;=%\-]/', '', $t);
}

/**
 * Display name — strip HTML, allow letters (incl. Unicode), digits, spaces, common punctuation.
 */
function instant_quote_sanitize_name(string $raw): string
{
    $t = strip_tags(trim($raw));
    if ($t === '') {
        return '';
    }
    $clean = preg_replace('/[^\p{L}\p{N}\s.\'\-,]/u', '', $t);
    if ($clean === null || $clean === '') {
        $clean = preg_replace('/[^a-zA-Z0-9\s.\'\-,]/', '', $t);
    }

    return strlen($clean) > 150 ? substr($clean, 0, 150) : trim($clean);
}

/**
 * Email from query — trimmed, de-HTML'd, confined to common email-like characters so partial
 * or marketing values (e.g. a.com) are still logged and passed through without strict RFC validation.
 */
function instant_quote_sanitize_email(string $raw): string
{
    $t = strip_tags(trim($raw));
    $t = str_replace(["\r", "\n", "\0", "\t"], '', $t);
    if ($t === '') {
        return '';
    }
    if (strlen($t) > 254) {
        $t = substr($t, 0, 254);
    }

    return preg_replace('/[^a-zA-Z0-9._@%+\-]/', '', $t);
}

$websiteUrl = instant_quote_sanitize_website_url((string) ($_GET['website_url'] ?? ''));
$visitorName = instant_quote_sanitize_name((string) ($_GET['name'] ?? ''));
$visitorEmail = instant_quote_sanitize_email((string) ($_GET['email'] ?? ''));

$logFile = __DIR__ . DIRECTORY_SEPARATOR . 'instant_quote_embed.log';

if ($websiteUrl !== '' || $visitorName !== '' || $visitorEmail !== '') {
    $ip = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? ''));
    if ($ip !== '' && strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip, 2)[0]);
    }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $uri = str_replace(["\r", "\n", "\0"], '', $uri);
    if (strlen($uri) > 2048) {
        $uri = substr($uri, 0, 2048) . '…';
    }

    $record = [
        'time'          => date('Y-m-d H:i:s'),
        'website_url'   => $websiteUrl,
        'name'          => $visitorName,
        'email'         => $visitorEmail,
        'ip'     => $ip,
        'uri'    => $uri,
        'host'   => (string) ($_SERVER['HTTP_HOST'] ?? ''),
        'referrer' => (string) ($_SERVER['HTTP_REFERER'] ?? ''),
        'agent'  => substr(str_replace(["\r", "\n"], ' ', (string) ($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 512),
    ];

    @file_put_contents(
        $logFile,
        json_encode($record, JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

$userIdEsc = htmlspecialchars('3ea9c883-70f8-8490-f1c4-6a05ce996166', ENT_QUOTES, 'UTF-8');
$accentEsc = htmlspecialchars('#6366f1', ENT_QUOTES, 'UTF-8');
$dataWebsiteUrl = htmlspecialchars($websiteUrl, ENT_QUOTES, 'UTF-8');
$dataEmbedName = htmlspecialchars($visitorName, ENT_QUOTES, 'UTF-8');
$dataEmbedEmail = htmlspecialchars($visitorEmail, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instant quote</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background: #f8fafc;
      color: #0f172a;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      font-size: 1rem;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      padding: clamp(0.75rem, 2vw, 1.5rem) clamp(0.75rem, 3vw, 1.75rem);
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      width: 100%;
    }

    main {
      max-width: 1280px;
      margin: 0 auto;
      width: 100%;
    }

    main > h1 {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      font-size: clamp(1.5rem, 0.35rem + 3.5vw, 3rem);
      font-weight: 700;
      text-align: center;
      letter-spacing: -0.03em;
      line-height: 1.15;
      color: #0f172a;
      padding: 0 24px;
      margin: 0;
    }
  </style>
</head>
<body>
  <main>
    <h1>Instant Quote Form</h1>
    <div id="limogen-widget"
         data-user-id="<?php echo $userIdEsc; ?>"
         data-width="100%"
         data-height="auto"
         data-accent-color="<?php echo $accentEsc; ?>"
         data-font-family="Inter"
         <?php if ($websiteUrl !== ''): ?>data-embed-website-url="<?php echo $dataWebsiteUrl; ?>"<?php endif; ?>
         <?php if ($visitorName !== ''): ?>data-embed-name="<?php echo $dataEmbedName; ?>"<?php endif; ?>
         <?php if ($visitorEmail !== ''): ?>data-embed-email="<?php echo $dataEmbedEmail; ?>"<?php endif; ?>></div>
  </main>
  <script src="https://zabrin.xyz/limogen-widget/widget.js" async></script>
</body>
</html>

<?php

/**
 * Append one line per authenticated page hit (when layout header is loaded).
 * Output: logs/session_logs/visits_YYYY-MM-DD.log
 */
function limo_log_session_visit(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $userId = trim((string) ($_SESSION['user']['id'] ?? ''));
    if ($userId === '') {
        return;
    }

    $logRoot = __DIR__;
    $logDir = $logRoot . '/session_logs';
    if (!is_dir($logDir) && !@mkdir($logDir, 0755, true) && !is_dir($logDir)) {
        return;
    }

    $file = $logDir . '/visits_' . date('Y-m-d') . '.log';

    $userLabel = trim((string) ($_SESSION['user']['user_name'] ?? ''));
    if ($userLabel === '') {
        $userLabel = trim((string) ($_SESSION['user']['email'] ?? ''));
    }

    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? ($_SERVER['SCRIPT_NAME'] ?? ''));
    $uri = str_replace(["\r", "\n", "\0"], '', $uri);
    if (strlen($uri) > 2048) {
        $uri = substr($uri, 0, 2048) . '…';
    }

    $ip = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? ''));
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip, 2)[0]);
    }

    $line = '[' . date('Y-m-d H:i:s') . ']'
        . ' user_id=' . $userId
        . ' user=' . str_replace(["\t", "\n", "\r"], ' ', $userLabel)
        . ' method=' . $method
        . ' uri=' . $uri
        . ' ip=' . $ip
        . PHP_EOL;

    @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}

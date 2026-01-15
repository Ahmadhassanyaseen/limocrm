<?php
function log_message($message, $level = 'INFO') {
    $logDir = __DIR__ . '/../logs';
    $logFile = $logDir . '/application_' . date('Y-m-d') . '.log';
    
    // Create logs directory if it doesn't exist
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Format the log message
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Write to the log file
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    // Also log to PHP's error log for visibility in development
    error_log("[$level] $message");
}

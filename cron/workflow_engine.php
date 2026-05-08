<?php
// Conditions-first workflow engine runner
// Run every 60 seconds:
//   php G:/XAMPP/htdocs/limocrm/cron/workflow_engine.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Workflows/Db.php';
require_once __DIR__ . '/../app/Workflows/WorkflowExecutionEngine.php';

$db = new Db($conn);
$engine = new WorkflowExecutionEngine($db);

$stats = $engine->runOnce();
echo json_encode(['success' => true, 'stats' => $stats], JSON_UNESCAPED_SLASHES) . "\n";


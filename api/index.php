<?php
// Simple API router for LimoCRM workflow engine
// Endpoints:
//   GET    /api/workflows
//   POST   /api/workflows
//   GET    /api/workflows/:id
//   PUT    /api/workflows/:id
//   DELETE /api/workflows/:id
//   GET    /api/workflows/module-fields/:module
//   GET    /api/email-templates
//   POST   /api/workflows/:id/execute-now
//   GET    /api/workflows/:id/logs

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Workflows/Db.php';
require_once __DIR__ . '/../app/Workflows/WorkflowController.php';

$db = new Db($conn);
$ctl = new WorkflowController($db);

function respond(int $code, array $payload) {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_SLASHES);
  exit;
}

function readJsonBody(): array {
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

// Remove everything before "/api/"
$pos = strpos($path, '/api/');
$sub = $pos === false ? $path : substr($path, $pos + 5);
$sub = trim($sub, '/');
$parts = $sub === '' ? [] : explode('/', $sub);

try {
  if ($method === 'GET' && $parts === ['workflows']) {
    respond(200, $ctl->list());
  }

  if ($method === 'POST' && $parts === ['workflows']) {
    $payload = readJsonBody();
    $out = $ctl->create($payload);
    respond($out['success'] ? 200 : 422, $out);
  }

  if (count($parts) >= 2 && $parts[0] === 'workflows') {
    $id = $parts[1];

    if ($method === 'GET' && count($parts) === 2) {
      $out = $ctl->get($id);
      respond($out['success'] ? 200 : 404, $out);
    }

    if ($method === 'PUT' && count($parts) === 2) {
      $payload = readJsonBody();
      $out = $ctl->update($id, $payload);
      respond($out['success'] ? 200 : 422, $out);
    }

    if ($method === 'DELETE' && count($parts) === 2) {
      respond(200, $ctl->delete($id));
    }

    if ($method === 'POST' && count($parts) === 3 && $parts[2] === 'execute-now') {
      respond(200, $ctl->executeNow($id));
    }

    if ($method === 'GET' && count($parts) === 3 && $parts[2] === 'logs') {
      respond(200, $ctl->logs($id));
    }
  }

  if ($method === 'GET' && count($parts) === 3 && $parts[0] === 'workflows' && $parts[1] === 'module-fields') {
    $module = urldecode($parts[2]);
    $out = $ctl->moduleFields($module);
    respond($out['success'] ? 200 : 422, $out);
  }

  if ($method === 'GET' && $parts === ['email-templates']) {
    respond(200, $ctl->emailTemplates());
  }

  respond(404, ['success' => false, 'message' => 'Not found', 'path' => $path]);
} catch (Throwable $e) {
  respond(500, ['success' => false, 'message' => $e->getMessage()]);
}


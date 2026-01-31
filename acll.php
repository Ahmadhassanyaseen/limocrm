<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * Yahan apne session/user structure ke mutabiq set karo:
 * Example screenshot suggests: $user['role_permissions']
 */
$user = $_SESSION['user'] ?? []; // <-- adjust if your session key differs
$permissions = $user['role_permissions'] ?? []; // <-- IMPORTANT

function normalize_module_key($m){
    $m = trim(strtolower((string)$m));
    $m = preg_replace('/\s+/', ' ', str_replace(['_', '-'], ' ', $m));

    $map = [
        'email templates' => 'email_templates',
        'emailtemplates'  => 'email_templates',
        'task'            => 'tasks',
        'tasks'           => 'tasks',
        'workflow'        => 'workflows',
        'workflows'       => 'workflows',
        'lead'            => 'leads',
        'leads'           => 'leads',
        'team'            => 'team',
        'vehicles'        => 'vehicles',
        'vehicle'         => 'vehicles',
        'roles'           => 'roles',
        'role management' => 'roles',
        'check'           => 'check',
        'integration'     => 'integration',
        'notes'           => 'notes',
        'dashboard'       => 'dashboard',
    ];

    return $map[$m] ?? str_replace(' ', '_', $m);
}

$acl = [];
if (is_array($permissions)) {
    foreach ($permissions as $p) {
        $key = normalize_module_key($p['module'] ?? '');
        if (!$key) continue;

        $acl[$key] = [
            'create' => !empty($p['can_create']) ? 1 : 0,
            'read'   => !empty($p['can_read']) ? 1 : 0,
            'update' => !empty($p['can_update']) ? 1 : 0,
            'delete' => !empty($p['can_delete']) ? 1 : 0,
        ];
    }
}

function can_show_module($acl, $moduleKey){
    if (!isset($acl[$moduleKey])) return false;
    $m = $acl[$moduleKey];
    return (!empty($m['read']) || !empty($m['create']) || !empty($m['update']) || !empty($m['delete']));
}

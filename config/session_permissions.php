<?php

declare(strict_types=1);

if (!function_exists('limo_nav_session_admin_full_access')) {
    function limo_nav_session_admin_full_access(): bool
    {
        $a = $_SESSION['user']['admin'] ?? 0;

        return (int) $a === 1;
    }
}

if (!function_exists('limo_role_permission_row_for_module')) {
    /**
     * @return array<string, mixed>|null
     */
    function limo_role_permission_row_for_module(string $module): ?array
    {
        $perms = $_SESSION['user']['role_permissions'] ?? [];
        if (!is_array($perms)) {
            return null;
        }
        $needle = strtolower(trim($module));
        foreach ($perms as $row) {
            if (!is_array($row) || !isset($row['module'])) {
                continue;
            }
            if (strtolower(trim((string) $row['module'])) === $needle) {
                return $row;
            }
        }

        return null;
    }
}

if (!function_exists('limo_user_module_access')) {
    /**
     * Session-based permission check (0 = denied, 1 = allowed).
     *
     * @param string $module  e.g. "Leads", "Contacts"
     * @param string $access  "create", "read", "update", or "delete" (case-insensitive)
     */
    function limo_user_module_access(string $module, string $access): int
    {
        if (limo_nav_session_admin_full_access()) {
            return 1;
        }
        $map = [
            'create' => 'can_create',
            'read' => 'can_read',
            'update' => 'can_update',
            'delete' => 'can_delete',
        ];
        $key = strtolower(trim($access));
        $col = $map[$key] ?? null;
        if ($col === null) {
            return 0;
        }
        $row = limo_role_permission_row_for_module($module);
        if ($row === null) {
            return 0;
        }

        return (int) ($row[$col] ?? 0) > 0 ? 1 : 0;
    }
}

if (!function_exists('limo_nav_can_module')) {
    function limo_nav_can_module(string $module): bool
    {
        if (limo_nav_session_admin_full_access()) {
            return true;
        }
        $row = limo_role_permission_row_for_module($module);
        if ($row === null) {
            return false;
        }
        $any = (int) ($row['can_create'] ?? 0)
            + (int) ($row['can_read'] ?? 0)
            + (int) ($row['can_update'] ?? 0)
            + (int) ($row['can_delete'] ?? 0);

        return $any > 0;
    }
}

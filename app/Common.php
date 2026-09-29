<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('has_permission')) {
    /**
     * Check if the currently logged-in user has a specific permission
     *
     * @param string|array $permission E.g. 'products.view' or ['products.view', 'products.create']
     * @return bool
     */
    function has_permission($permission): bool
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return false;
        }

        // Super Admin bypasses all permission checks
        if ($session->get('role_name') === 'super_admin') {
            return true;
        }

        $userPerms = $session->get('permissions') ?? [];

        if (is_array($permission)) {
            foreach ($permission as $p) {
                if (!empty($userPerms[$p])) {
                    return true;
                }
            }
            return false;
        }

        return !empty($userPerms[$permission]);
    }
}

if (!function_exists('has_module_access')) {
    /**
     * Check if the currently logged-in user has access to a module (any action in that module)
     *
     * @param string $module E.g. 'products', 'sales', 'warehouses'
     * @return bool
     */
    function has_module_access(string $module): bool
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return false;
        }

        // Super Admin has full system access
        if ($session->get('role_name') === 'super_admin') {
            return true;
        }

        // Dashboard is accessible to all logged-in users
        if ($module === 'dashboard') {
            return true;
        }

        $userPerms = $session->get('permissions') ?? [];
        $prefix = strtolower(str_replace('-', '_', $module)) . '.';

        foreach ($userPerms as $permKey => $val) {
            if ($val && strpos($permKey, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }
}

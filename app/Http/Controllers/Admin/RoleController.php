<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::whereIn('name', ['admin', 'agent'])
            ->with('permissions')
            ->get();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function edit(Role $role): Response
    {
        abort_if($role->name === 'manager', 403, 'As permissões do manager não podem ser alteradas.');

        $role->load('permissions');

        return Inertia::render('Admin/Roles/Edit', [
            'role'        => $role,
            'permissions' => Permission::orderBy('name')->get(['id', 'name', 'display_name']),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'manager', 403, 'As permissões do manager não podem ser alteradas.');

        $request->validate([
            'permissions'   => ['present', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', "Permissões de \"{$role->display_name}\" atualizadas.");
    }
}

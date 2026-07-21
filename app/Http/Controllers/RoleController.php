<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPermissions;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Support\PermissionCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesPermissions;

    public function index(): View
    {
        $this->authorizePermission('view-roles');

        $roles = Role::query()
            ->with('permissions')
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorizePermission('create-roles');

        return view('roles.create', [
            'permissionGroups' => PermissionCatalog::groups(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorizePermission('create-roles');

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->input('name'),
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($request->input('permissions', []));
        });

        return redirect()->route('roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح.');
    }

    public function edit(Role $role): View
    {
        $this->authorizePermission('edit-roles');

        $role->load('permissions');

        return view('roles.edit', [
            'role' => $role,
            'permissionGroups' => PermissionCatalog::groups(),
            'assignedPermissions' => $role->permissions->pluck('name')->all(),
            'isProtected' => PermissionCatalog::isProtectedRole($role->name),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorizePermission('edit-roles');

        DB::transaction(function () use ($request, $role) {
            if (! PermissionCatalog::isProtectedRole($role->name)) {
                $role->update(['name' => $request->input('name')]);
            }

            $role->syncPermissions($request->input('permissions', []));
        });

        return redirect()->route('roles.index')
            ->with('success', 'تم تحديث الدور بنجاح.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorizePermission('delete-roles');

        if (PermissionCatalog::isProtectedRole($role->name)) {
            return back()->with('error', 'لا يمكن حذف هذا الدور المحمي.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف دور مرتبط بمستخدمين.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'تم حذف الدور بنجاح.');
    }
}

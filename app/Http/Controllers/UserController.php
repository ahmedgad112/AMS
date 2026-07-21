<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPermissions;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesPermissions;

    public function index(): View
    {
        $this->authorizePermission('view-users');

        $users = User::with('roles', 'schoolClasses')
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorizePermission('create-users');

        return view('users.create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorizePermission('create-users');
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
            ]);

            $user->syncRoles([$request->input('role')]);
            $this->syncClassAssignment($user, $request->input('role'), $request->input('class_ids', []));
        });

        return redirect()->route('users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    public function edit(User $user): View
    {
        $this->authorizePermission('edit-users');

        $user->load('roles', 'schoolClasses');

        return view('users.edit', array_merge(['user' => $user], $this->formData()));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorizePermission('edit-users');
        DB::transaction(function () use ($request, $user) {
            $data = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            $user->update($data);
            $user->syncRoles([$request->input('role')]);
            $this->syncClassAssignment($user, $request->input('role'), $request->input('class_ids', []));
        });

        return redirect()->route('users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizePermission('delete-users');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }

    protected function formData(): array
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return [
            'roles' => $roles,
            'classes' => SchoolClass::orderBy('name')->get(),
            'rolesAccessAll' => $roles->mapWithKeys(
                fn ($role) => [$role->name => $role->hasPermissionTo('access-all-classes')]
            ),
        ];
    }

    protected function syncClassAssignment(User $user, string $roleName, array $classIds): void
    {
        $role = Role::findByName($roleName, 'web');

        if ($role->hasPermissionTo('access-all-classes')) {
            $user->schoolClasses()->detach();
        } else {
            $user->schoolClasses()->sync($classIds);
        }
    }
}

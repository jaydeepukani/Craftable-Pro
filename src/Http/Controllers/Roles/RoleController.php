<?php

namespace Brackets\CraftablePro\Http\Controllers\Roles;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Roles\IndexRoleRequest;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoleController extends Controller
{
    public function index(IndexRoleRequest $request)
    {
        $roles = QueryBuilder::for(Role::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id',
                    'name',
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts(['id', 'name'])
            ->with('users')
            ->select(['id', 'name', 'two_factor_auth_required'])
            ->paginate(request()->get('per_page'))->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('craftable-pro.role.store');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'two_factor_auth_required' => ['required', 'boolean'],
        ]);
        Role::create([
            'name' => $data['name'],
            'guard_name' => 'craftable-pro',
            'two_factor_auth_required' => $data['two_factor_auth_required'],
        ]);

        return back()->with(['message' => ___('craftable-pro', 'Role has been successfully created')]);
    }

    public function edit(Role $role)
    {
        $this->authorize('craftable-pro.role.edit');

        $allPermissions = Permission::all()->map->name;
        $assignedPermissions = $role->permissions->map->name;

        $permissionsTree = [];

        collect($allPermissions)->each(function ($permission) use (&$permissionsTree, $assignedPermissions) {
            $isAssigned = collect($assignedPermissions)->contains($permission);
            Arr::set($permissionsTree, $permission, $isAssigned);
        });

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissionsTree' => $permissionsTree,
        ]);
    }

    public function update(Role $role, Request $request)
    {
        $this->authorize('craftable-pro.role.edit');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'two_factor_auth_required' => ['required', 'boolean'],
        ]);
        $role->update([
            'name' => $data['name'],
            'two_factor_auth_required' => $data['two_factor_auth_required'],
        ]);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Role has been successfully updated')]);
    }

    public function destroy(Request $request, Role $role)
    {
        $this->authorize('craftable-pro.role.destroy');

        if ($role->users()->count() === 0) {
            $role->delete();
        } else {
            return back()->withErrors([
                ___('craftable-pro', "This role is in use by :count user(s). Please change the role for these users first.", ['count' => $role->users()->count()]),
            ]);
        }

        return back()->with(['message' => ___('craftable-pro', 'Role has been successfully deleted')]);
    }
}

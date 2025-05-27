<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // For MongoDB, we need to handle withCount differently
        $roles = Role::all()->map(function($role) {
            $role->users_count = $role->users()->count();
            $role->permissions_count = $role->permissions()->count();
            return $role;
        });
        
        // Manual pagination for MongoDB
        $page = request()->get('page', 1);
        $perPage = 15;
        $roles = new \Illuminate\Pagination\LengthAwarePaginator(
            $roles->forPage($page, $perPage),
            $roles->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        
        return view('admin.role.index', [
            'menu' => 'User Management',
            'menu_name' => 'Roles',
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $permissionGroups = $permissions->groupBy('group');
        
        return view('admin.role.create', [
            'menu' => 'User Management',
            'menu_name' => 'Create Role',
            'permissionGroups' => $permissionGroups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);
        
        // Assign permissions if provided
        if ($request->has('permissions')) {
            $role->assignPermissions($request->permissions);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        // For MongoDB, manually count related users instead of using loadCount
        $role->users_count = $role->users()->count();
        
        return view('admin.role.show', [
            'menu' => 'User Management',
            'menu_name' => 'Role Details',
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $permissionGroups = $permissions->groupBy('group');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
        
        return view('admin.role.edit', [
            'menu' => 'User Management',
            'menu_name' => 'Edit Role',
            'role' => $role,
            'permissionGroups' => $permissionGroups,
            'rolePermissionIds' => $rolePermissionIds
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);
        
        // Update permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Role cannot be deleted because it is assigned to users.');
        }
        
        $role->permissions()->detach();
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}

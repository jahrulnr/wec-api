<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->paginate(20);
        
        return view('admin.permission.index', [
            'menu' => 'User Management',
            'menu_name' => 'Permissions',
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique permission groups for the dropdown
        $permissionGroups = Permission::select('group')->distinct()->pluck('group')->filter()->toArray();
        
        return view('admin.permission.create', [
            'menu' => 'User Management',
            'menu_name' => 'Create Permission',
            'permissionGroups' => $permissionGroups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:permissions'],
            'description' => ['nullable', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate slug from name if not provided
        $slug = $request->slug ?? Str::slug($request->name, '-');

        Permission::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'group' => $request->group,
        ]);
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        // Load roles that have this permission
        $permission->load('roles');
        
        return view('admin.permission.show', [
            'menu' => 'User Management',
            'menu_name' => 'Permission Details',
            'permission' => $permission
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        // Get unique permission groups for the dropdown
        $permissionGroups = Permission::select('group')->distinct()->pluck('group')->filter()->toArray();
        
        return view('admin.permission.edit', [
            'menu' => 'User Management',
            'menu_name' => 'Edit Permission',
            'permission' => $permission,
            'permissionGroups' => $permissionGroups
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate slug from name if not provided
        $slug = $request->slug ?? Str::slug($request->name, '-');

        $permission->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'group' => $request->group,
        ]);
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Permission cannot be deleted because it is assigned to roles.');
        }
        
        $permission->delete();
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}

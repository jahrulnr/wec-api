<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management permissions
            ['name' => 'View Users', 'slug' => 'users-view', 'group' => 'User Management'],
            ['name' => 'Create Users', 'slug' => 'users-create', 'group' => 'User Management'],
            ['name' => 'Edit Users', 'slug' => 'users-edit', 'group' => 'User Management'],
            ['name' => 'Delete Users', 'slug' => 'users-delete', 'group' => 'User Management'],
            
            // Role management permissions
            ['name' => 'View Roles', 'slug' => 'roles-view', 'group' => 'Role Management'],
            ['name' => 'Create Roles', 'slug' => 'roles-create', 'group' => 'Role Management'],
            ['name' => 'Edit Roles', 'slug' => 'roles-edit', 'group' => 'Role Management'],
            ['name' => 'Delete Roles', 'slug' => 'roles-delete', 'group' => 'Role Management'],
            
            // Permission management permissions
            ['name' => 'View Permissions', 'slug' => 'permissions-view', 'group' => 'Permission Management'],
            ['name' => 'Create Permissions', 'slug' => 'permissions-create', 'group' => 'Permission Management'],
            ['name' => 'Edit Permissions', 'slug' => 'permissions-edit', 'group' => 'Permission Management'],
            ['name' => 'Delete Permissions', 'slug' => 'permissions-delete', 'group' => 'Permission Management'],
            
            // API Switcher permissions
            ['name' => 'Manage API Switcher', 'slug' => 'api-switcher-manage', 'group' => 'API Management'],
            ['name' => 'View API Switcher', 'slug' => 'api-switcher-view', 'group' => 'API Management'],

            ['name' => 'Testing', 'slug' => 'postman', 'group' => 'Postman Feature'],
        ];
        
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
        
        // Create roles
        $adminRole = Role::create([
            'name' => 'Administrator',
            'description' => 'Administrator with full access to all features',
            'is_active' => true,
        ]);
        
        $managerRole = Role::create([
            'name' => 'Manager',
            'description' => 'Manager with limited administrative access',
            'is_active' => true,
        ]);
        
        $userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user with basic access',
            'is_active' => true,
        ]);
        
        $apiRole = Role::create([
            'name' => 'API Manager',
            'description' => 'User with access to manage API settings',
            'is_active' => true,
        ]);
        
        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::all());
        
        $managerRole->permissions()->attach(
            Permission::whereIn('slug', [
                'users-view', 'users-create', 'users-edit',
                'roles-view',
                'api-switcher-view', 'api-switcher-manage'
            ])->get()
        );
        
        $userRole->permissions()->attach(
            Permission::whereIn('slug', [
                'api-switcher-view'
            ])->get()
        );
        
        $apiRole->permissions()->attach(
            Permission::whereIn('slug', [
                'api-switcher-view', 'api-switcher-manage'
            ])->get()
        );
        
        // Create admin user if it doesn't exist
        $admin = User::where('email', 'test@example.com')->first();
        if ($admin) {            
            $admin->roles()->attach($adminRole);
        }
    }
}

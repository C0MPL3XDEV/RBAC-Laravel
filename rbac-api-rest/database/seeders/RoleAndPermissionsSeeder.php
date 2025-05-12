<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions to avoid cache conflict errors
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permission you want to create to assigns to roles
        $permissions = [
            'create-product',
            'edit-product',
            'delete-product',
            'view-product',
            'view-users',
            'edit-users',
            'delete-users',
            'create-users',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-roles',
        ];

        // Cycle to create all permissions in the array
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']); // Create permission
        }

        // Define standard role to create
        $roles = [
            'super-admin' => Permission::all()->pluck('name')->toArray(), // Get all permissions name array
            'delegated-admin' => [
                'create-product',
                'edit-product',
                'view-product'
            ],
            'viewer' => [
                'view-product'
            ]
        ];

        // Cycle to create all roles in the array with correct permissions
        foreach ($roles as $role => $rolePermissions) {
            $roleModel = Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']); // Create role

            // Get the permission model for each (permissions) for each role and set permissions to the role
            foreach ($rolePermissions as $permission) {
                $permissionModel = Permission::where('name', $permission)->first(); // Get Permission model
                if ($permissionModel) { // Check if the permission was found
                    $roleModel->givePermissionTo($permissionModel); // Set Permission to the role
                }
            }
        }
    }
}

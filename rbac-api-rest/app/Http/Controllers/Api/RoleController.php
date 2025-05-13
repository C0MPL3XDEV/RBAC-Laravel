<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    // Get all roles with permissions associated
    public function index(Request $request): JsonResponse {

        // Check if the user have the Spatie permission 'view-roles'
        if (!$request->user()->can('view-roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view roles.'
            ], 403);
        }

        // Get all Roles with Permissions relation to get permissions associated to the roles
        $roles_with_permissions = Role::with('permissions')->get();

        return response()->json([
            'success' => true,
            'data' => $roles_with_permissions
        ], 200);
    }

    // Create a new role with permissions associated if passed
    public function store(Request $request): JsonResponse {

        // Validate data to create role
        $validated = $request->validate([
            'role_name' => 'required:unique:roles,name|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name' // Validation for permissions array elements
        ]);

        // Check if the user have the permission to create a role
        if (!$request->user()->can('create-roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create roles.'
            ]);
        }

        $role = Role::create(['name' => $validated['role_name']]); // Role creation without permissions associated

        // Check if the user passed the permission for the role
        if ($request->has('permissions')) {
            // Replace all current permission of the role with new permissions passed
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'data' => $role->load('permissions'), // Load Role with Permissions relation to get permissions associated
        ]);

    }

}

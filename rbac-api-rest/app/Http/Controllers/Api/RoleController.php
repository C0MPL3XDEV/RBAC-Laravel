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
            ], 403);
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

    public function update(Request $request, Role $role): JsonResponse {

        // The $role parameter is automatically injected by Laravel's route model binding,
        // and contains the Role instance corresponding to the ID provided in the route.

        // Validate data to update role
        $validated = $request->validate([
            'role_name' => 'required|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Check if the user have the permission to update role
        if (!$request->user()->can('edit-roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update roles.'
            ], 403);
        }

        // Execute the update of the name of the role
        $role->update(['name' => $validated['role_name']]);

        // Check if in the request we have permissions array for the role permission update
        if ($request->has('permissions')) {
            // Replace all current permission of the role with new permissions passed
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'data' => $role->load('permissions'), // Load role and return the role data with permission updated
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse {

        // The $role parameter is automatically injected by Laravel's route model binding,
        // and contains the Role instance corresponding to the ID provided in the route.
        $role = Role::findById($id);

        // Check if the user have the permission to delete the role
        if (!$request->user()->can('delete-roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete roles.'
            ], 403);
        }

        // Check if the role want to delete the user is super-admin and block the delete action
        if ($role->name == "super-admin") {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete super-admin role.'
            ], 403);
        }

        // TODO: Implement SoftDelete and Restore

        // Deletes the role. Assigned permissions remain in the permissions table,
        // but the pivot relationships (e.g. with users) are removed automatically.

        try {
            $role->delete();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ], 200);
    }

    public function getRoleById(Request $request, $id): JsonResponse
    {
        // The $role parameter is automatically injected by Laravel's route model binding,
        // and contains the Role instance corresponding to the ID provided in the route.

        $role = Role::with('permissions')->findOrFail($id);

        // Check if the user have the permission to view roles
        if (!$request->user()->can('view-roles')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view roles.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $role->load('permissions'), // Return the role with permission associated
        ]);

    }

    public function assignPermission(Role $role, Request $request): JsonResponse {

        // The $role parameter is automatically injected by Laravel's route model binding,
        // and contains the Role instance corresponding to the ID provided in the route.

        // Validate data to assign permission to the role
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        // Check if the user have the permission to assign a permission to the role
        if (!$request->user()->can('assign-permissions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to assign permissions.'
            ], 403);
        }

        // Adds the specified permission to the role, keeping all existing permissions intact.
        $role->givePermissionTo($validated['permission']);

        return response()->json([
            'success' => true,
            'message' => 'Permission assigned successfully.',
            'data' => $role->load('permissions'), // Return the role data with the permissions updated
        ], 200);
    }

    public function revokePermission(Role $role, Request $request): JsonResponse {

        // The $role parameter is automatically injected by Laravel's route model binding,
        // and contains the Role instance corresponding to the ID provided in the route.

        // Validate data to revoke permission to the role
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        // Check if the user have the permission to revoke a permission to the role
        if (!$request->user()->can('revoke-permissions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to revoke permissions.'
            ], 403);
        }

        $role->revokePermissionTo($validated['permission']); // Revoke a specific permission to the role

        return response()->json([
            'success' => true,
            'message' => 'Permission revoked successfully.',
        ], 200);
    }
}

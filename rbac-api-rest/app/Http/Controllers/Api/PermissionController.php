<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct() {
        // If u want u can put control based on role or permission in the constructor of the controller
        // Like this - $this->middleware('can:view-permissions')->only(['index]) this specify the middleware
        // to check the user permissions ad declare in which method are applied
    }

    public function index(Request $request): JsonResponse {

        // Check if the user have the permission to view permission
        if (!$request->user()->can('view-permissions')) {
            return response()->json([
               "success" => false,
               "message" => "You do not have permission to view permissions.",
            ], 403);
        }

        // Retrieve all permissions from Permission model along with the Roles relation to roles associated with each permission
        $permissions = Permission::with('roles')->get()->pluck('name')->toArray();

        return response()->json([ // Return the response with data
            "success" => true,
            "permissions" => $permissions,
        ]);
    }

    public function store(Request $request): JsonResponse {

        // Validate the data to create the permission
        $validated = $request->validate([
            "permission_name" => "required|unique:permissions,name|string",
        ]);

        // Check if the user have the permission to create
        if (!$request->user()->can('create-permissions')) {
            return  response()->json([
                "success" => false,
                "message" => "You do not have permission to create permissions.",
            ], 403);
        }

        try {
            // Create the permission with the name validated
            $create_permission = Permission::create(['name' => $validated["permission_name"]]);

            return response()->json([ // Return the new permission created
                "success" => true,
                "permission" => $create_permission,
            ],200);
        } catch (\Exception $e) { // Catch Error
            return response()->json([
                "success" => false,
                "message" => "An error occurred while creating permission.",
                "error" => $e->getMessage(), // Error Message
            ], 500);
        }
    }

    public function update(Request $request, $permissionId): JsonResponse {

        $validated = $request->validate([
            "permission_name" => "required|string",
        ]);

        if  (!$request->user()->can('update-permissions')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to update permissions.",
            ], 403);
        }

        try  {
            $permission = Permission::findById($permissionId);
            $permission->update(['name' => $validated["permission_name"]]);

            return response()->json([
                "success" => true,
                "message" => "Permission updated successfully.",
                "permission" => $permission,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "An error occurred while updating permission.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $permissionId): JsonResponse {

        if (!$request->user()->can('delete-permissions')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to delete permissions.",
            ], 403);
        }

        try {
            $permission = Permission::findById($permissionId);
            $permission->users()->detach();
            $permission->roles()->detach();
            $permission->delete();

            return response()->json([
                "success" => true,
                "message" => "Permission deleted successfully.",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "An error occurred while deleting permission.",
                "error" => $e->getMessage(),
            ], 500);
        }

    }

    public function getPermissionById(Request $request, $permissionId): JsonResponse {

        if (!$request->user()->can('view-permissions')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to view permissions.",
            ], 403);
        }

        try {
            $permission = Permission::findById($permissionId)->with('roles')->firstOrFail();

            return response()->json([
                "success" => true,
                "permission" => $permission,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message1" => "An error occurred while getting permission.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function assignToRole(Request $request, $roleId): JsonResponse {

        $validated = $request->validate([
            "permissions" => "required|array",
            "permissions.*" => "required|string|exists:permissions,name",
        ]);

        if (!$request->user()->can('update-permissions')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to update permissions.",
            ], 403);
        }

        try {
            $role = Role::findOrFail($roleId);
            $role->givePermissionTo($validated["permissions"]);

            return response()->json([
                "success" => true,
                "message" => "Permission assigned to the role successfully.",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "An error occurred while assign permission to the role.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function revokeFromRole(Request $request, $roleId): JsonResponse {

        $validated = $request->validate([
            "permissions" => "required|array",
            "permissions.*" => "required|string|exists:permissions,name",
        ]);

        if (!$request->user()->can("revoke-permissions")) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to revoke permissions.",
            ], 403);
        }

        return response()->json([]);
    }

}

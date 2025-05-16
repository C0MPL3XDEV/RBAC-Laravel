<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // Check if the user can view users list
        if (!$request->user()->can('view-users')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to view users",
            ]);
        }

        $users = User::paginate(10); // Return paginated users

        // Apply a transform to elements of the collection returned by the paginator $users
        $users->getCollection()->transform(function ($user) {
           // Return associative array with custom data for each user in the collection
           return [
               "id" => $user->id, // User ID
               "name" => $user->name, // User Name
               "email" => $user->email, // User Email
               // User Time Stamps
               "created_at" => $user->created_at,
               "updated_at" => $user->updated_at,
               "roles" => $user->getRoleNames(), // User Role Names
               "permissions" => $user->getAllPermissions()->pluck('name'), // All user permission plucked by name ( Role permissions associated and Direct permission associated )
           ];
        });

        return response()->json([
            "success" => true,
            "data" => $users, // Return users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request): JsonResponse
    {
        // Check if the user have the permission to create user
        if (!$request->user()->can('create-users')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to create users",
            ], 403);
        }

        // Validate data to create user
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            "roles" => "nullable|array",
            "roles.*" => "exists:roles,name",
        ]);

        // Create the user with data validated
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Assign roles validated to the user if passed
        if ($request->has('roles') && !empty($validatedData['roles'])) {
            $user->syncRoles($validatedData['roles']);
        }

        return response()->json([
            "success" => true,
            "user" => $user, // Return data of the user created
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        // The $user parameter is automatically injected by Laravel's route model binding,
        // and contains the User model instance corresponding to the ID provided in the route.

        // Check if the user have the permission to edit a user
        if (!$request->user()->can('edit-users')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to update users",
            ], 403);
        }

        // Validate the data to update
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            "roles" => "nullable|array",
            "roles.*" => "exists:roles,name"
        ]);

        $user->update([ // Update the user data
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        if ($request->has('roles') && !empty($validatedData['roles'])) { // Update user roles if passed
            $user->syncRoles($validatedData['roles']); // Sync roles of the user
        }

        return response()->json([
            "success" => true,
            "user" => $user, // Return the user with updated
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        // The $user parameter is automatically injected by Laravel's route model binding,
        // and contains the User model instance corresponding to the ID provided in the route.

        // Check if the user have the permission to delete a user
        if (!$request->user()->can('delete-users')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to delete users",
            ], 403);
        }

        // Check if the user to delete having a super-admin role and block the action
        if ($user->hasRole('super-admin')) {
            return response()->json([
                "success" => false,
                "message" => "You can't delete super-admin users",
            ], 403);
        }

        $user->roles()->detach(); // Before to delete user detach the roles relationship
        $user->permissions()->detach(); // Before to delete user detach the permissions relationships

        $user->delete(); // Delete user

        return response()->json([
            "success" => true,
            "message" => "User deleted successfully!",
        ], 200);
    }

    public function getUserById(Request $request, User $user): JsonResponse {

        if (!$request->user()->can('view-users')) {
            return response()->json([
                "success" => false,
                "message" => "You do not have permission to view users",
            ], 403);
        }

        return response()->json([
            "success" => true,
            "data" => $user,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($id = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo('View Users')) {
            return $this->sendError("Can't view users");
        }

        if ($id && $id > 0 && $id < 100) {
            $users = User::with(['roles', 'permissions'])->withTrashed()->paginage($id); //ask in request is greater than zero and is less than 100 then
        } else {
            $users = User::with(['roles', 'permissions'])->withTrashed()->paginage(100); //default 100 users per request
        }

        return $this->sendResponse($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermissionTo('Add users')) {
            return $this->sendError("Can't add user", 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique',
            'profile' => 'string',
            'country' => 'string',
            'phone' => 'required|string',
            'postalCode' => 'required',
            'city' => 'string',
            'state' => 'string',
            'role' => 'string|exists,roles,name|not_in:executive',
            'address' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = $validator->validated();
        $user = User::create($data);
        $role = Role::findByName($request->role);

        $user->assignRole($role);
        return $this->sendResponse($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        $targetUser = User::with(['roles.permissions'])->find($id);

        if (!$user || !$targetUser || (!$targetUser->hasPermissionTo('View Users') && $user != $targetUser)) {
            return $this->sendError();
        }

        return $this->sendResponse($user);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        // Check if the user is updating their own record or has the 'Edit users' permission
        if (!$user || ($user->id != $id && !$user->hasPermissionTo('Edit users'))) {
            return $this->sendError("You can't update this user", 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|string|unique:users,email,' . $id,
            'profile' => 'sometimes|string',
            'country' => 'sometimes|string',
            'phone' => 'sometimes|required|string',
            'postalCode' => 'sometimes|required',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string',
            'role' => 'sometimes|string|exists:roles,name|not_in:executive',
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Find user by ID
        $userToUpdate = User::findOrFail($id);

        if (!$userToUpdate) {
            return $this->sendError('Invalid User Id');
        }
        // Update user data
        $userToUpdate->update($validator->validated());

        // Update role if provided
        if ($request->has('role')) {
            $role = Role::findByName($request->role);
            $userToUpdate->syncRoles([$role]);
        }

        return $this->sendResponse($userToUpdate, 'User successfully updated!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        // Check if the user is deleting their own record or has the permission
        if (!$user || !$user->hasPermissionTo('Deactivate users')) {
            return $this->sendError("You can't deactivate this user", 403);
        }

        if ($user->id == $id) {
            return $this->sendError("You can't deactivate own account", 403);
        }

        // Find user by ID
        $userToupdate = User::findOrFail($id);

        if (!$userToupdate) {
            return $this->sendError('Record not found');
        }

        // Deactivate user
        $userToupdate->delete();

        return $this->sendResponse('User successfully deactivated!');
    }

    public function activate($id)
    {
        $user = auth()->user();

        // Check if the user is deleting their own record or has the permission
        if (!$user || !$user->hasPermissionTo('Activate users')) {
            return $this->sendError("You can't Activate this user", 403);
        }

        // Find user by ID
        $userToupdate = User::withTrashed()->find($id);

        if (!$userToupdate) {
            return $this->sendError('Record not found');
        }
        // activate user
        $userToupdate->restore();

        return $this->sendResponse('User successfully Activated!');
    }

}

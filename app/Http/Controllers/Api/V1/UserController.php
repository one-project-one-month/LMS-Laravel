<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate(15);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'      => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'dob'           => 'nullable|date',
            'address'       => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role_id'       => 'nullable|exists:roles,id',
            'is_available'  => 'boolean'
        ]);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file->isValid()) {
                $filename = $validated['username'] . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_photos', $filename, 'public');
                $validated['profile_photo'] = $path;
            } else {
                return response()->json([
                    'error' => 'Invalid file upload: ' . $file->getErrorMessage()
                ], 422);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = $validated['role_id'] ?? 1;

        $user = User::create($validated);

        return response()->json(['data' => $user], 201);
    }


    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        Log::info($request->all());

        $validated = $request->validate([
            'username'      => 'sometimes|required|string|max:255',
            'email'         => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'      => 'sometimes|nullable|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'dob'           => 'nullable|date',
            'address'       => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role_id'       => 'nullable|exists:roles,id',
            'is_available'  => 'boolean'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $file = $request->file('profile_photo');
            $username = $validated['username'] ?? $user->username;
            $filename = $username . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $validated['profile_photo'] = $path;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }


    public function destroy(User $user)
    {
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}

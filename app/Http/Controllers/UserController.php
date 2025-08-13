<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        // Check if user can view users
        if (!auth()->user()->can('viewAny', User::class)) {
            abort(403, 'Only administrators can manage users.');
        }

        $users = User::orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Check if user can create users
        if (!auth()->user()->can('create', User::class)) {
            abort(403, 'Only administrators can create users.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Check if user can create users
        if (!auth()->user()->can('create', User::class)) {
            abort(403, 'Only administrators can create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,agent',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        // Check if user can update this user
        if (!auth()->user()->can('update', $user)) {
            abort(403, 'Only administrators can edit users.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Check if user can update this user
        if (!auth()->user()->can('update', $user)) {
            abort(403, 'Only administrators can edit users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,agent',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        // Update password only if provided
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Check if user can delete this user
        if (!auth()->user()->can('delete', $user)) {
            abort(403, 'Only administrators can delete users.');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        // Check if user has assigned leads
        if ($user->leads()->count() > 0) {
            return redirect()->route('users.index')->with('error', 'Cannot delete user with assigned leads. Reassign leads first.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}

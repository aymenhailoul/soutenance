<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->orderBy('name')->get(),
            'roles' => ['Admin', 'Super User', 'User'],
        ]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => ['Admin', 'Super User', 'User'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:users,name'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['Admin', 'Super User', 'User'])],
        ]);

        User::create($data);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('users', 'name')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['Admin', 'Super User', 'User'])],
        ]);

        // If password is empty, don't overwrite it.
        if (($data['password'] ?? null) === null || $data['password'] === '') {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors(['delete' => 'You cannot delete your own user.']);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Page;
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
            'users' => User::with('pages')->orderBy('name')->get(),
            'pages' => Page::orderBy('name')->get(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'pages' => Page::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:users,name'],
            'password' => ['required', 'string', 'min:6'],
            'pages' => ['nullable', 'array'],
            'pages.*' => ['exists:pages,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'password' => $data['password'],
        ]);

        // Attach selected pages
        // Attach selected pages
        $pagesToAttach = $data['pages'] ?? [];

        // Always attach dashboard permission if not already selected
        $dashboardPage = Page::where('route', 'dashboard')->first();
        if ($dashboardPage && !in_array($dashboardPage->id, $pagesToAttach)) {
            $pagesToAttach[] = $dashboardPage->id;
        }

        if (!empty($pagesToAttach)) {
            $user->pages()->attach($pagesToAttach);
        }

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('users', 'name')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'pages' => ['nullable', 'array'],
            'pages.*' => ['exists:pages,id'],
        ]);

        // Update user basic info
        $updateData = ['name' => $data['name']];

        // If password is provided, update it
        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);

        // Sync pages (this will remove old permissions and add new ones)
        $user->pages()->sync($data['pages'] ?? []);

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

    public function storePage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'route' => ['required', 'string', 'max:255', 'unique:pages,route'],
        ]);

        Page::create($data);

        return back()->with('success', 'Page created successfully.');
    }

    public function updatePage(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'route' => ['required', 'string', 'max:255', Rule::unique('pages', 'route')->ignore($page->id)],
        ]);

        $page->update($data);

        return back()->with('success', 'Page updated successfully.');
    }

    public function destroyPage(Page $page): RedirectResponse
    {
        $page->delete();

        return back()->with('success', 'Page deleted successfully.');
    }
}

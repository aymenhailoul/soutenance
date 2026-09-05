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
    /**
     * Pages auto-assigned per role.
     * Admin gets everything; Manager, Technician, Viewer get progressively fewer.
     */
    protected function rolePageRoutes(string $role): array
    {
        $technicianRoutes = [
            'dashboard',
            'equipment.index',
            'categories.index',
            'assignments.index',
            'maintenances.index',
            'clients.index',
            'sites.index',
            'stock.index',
        ];

        $managerRoutes = array_merge($technicianRoutes, [
            'reports.index',
            'backups.index',
        ]);

        $adminRoutes = array_merge($managerRoutes, [
            'users.index',
            'employees.index',
        ]);

        $viewerRoutes = [
            'dashboard',
            'equipment.index',
            'categories.index',
            'assignments.index',
        ];

        return match ($role) {
            'Admin' => $adminRoutes,
            'Manager' => $managerRoutes,
            'Technician' => $technicianRoutes,
            'Viewer' => $viewerRoutes,
            default => ['dashboard'],
        };
    }

    /**
     * Retrieve page IDs for given route names.
     */
    protected function pageIdsForRoutes(array $routes): array
    {
        return Page::whereIn('route', $routes)->pluck('id')->toArray();
    }

    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('pages')->orderBy('name')->get(),
            'pages' => Page::orderBy('name')->get(),
            'roles' => ['Admin', 'Manager', 'Technician', 'Viewer'],
        ]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'pages' => Page::orderBy('name')->get(),
            'roles' => ['Admin', 'Manager', 'Technician', 'Viewer'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:users,name', 'regex:/^[\pL\s\-]+$/u'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Technician', 'Viewer'])],
            'pages' => ['nullable', 'array'],
            'pages.*' => ['integer', 'exists:pages,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        // Assign pages: if pages checkboxes were submitted in the form, use exact user selection; otherwise fallback to default role pages.
        if ($request->has('pages_submitted') || $request->has('pages')) {
            $pagesToAttach = $data['pages'] ?? [];
        } else {
            $pagesToAttach = $this->pageIdsForRoutes($this->rolePageRoutes($data['role']));
        }

        if (!empty($pagesToAttach)) {
            $user->pages()->attach($pagesToAttach);
        }

        return redirect()->route('users.index')->with('success', "Utilisateur '{$user->name}' créé avec le rôle {$data['role']}.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('users', 'name')->ignore($user->id), 'regex:/^[\pL\s\-]+$/u'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Technician', 'Viewer'])],
            'pages' => ['nullable', 'array'],
            'pages.*' => ['integer', 'exists:pages,id'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);

        // Re-sync pages: use exact user selection if submitted; otherwise fallback to default role pages
        if ($request->has('pages_submitted') || $request->has('pages')) {
            $pagesToSync = $data['pages'] ?? [];
        } else {
            $pagesToSync = $this->pageIdsForRoutes($this->rolePageRoutes($data['role']));
        }

        $user->pages()->sync($pagesToSync);

        return redirect()->route('users.index')->with('success', "Utilisateur '{$user->name}' mis à jour avec le rôle {$data['role']}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors(['delete' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function storePage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'route' => ['required', 'string', 'min:2', 'max:100', 'unique:pages,route', 'regex:/^[a-zA-Z0-9\.\_\-]+$/'],
        ]);

        $page = Page::create($data);

        // Auto-assign newly created page to creator & all Admin users so it immediately appears in sidebar
        $adminUserIds = User::where('role', 'Admin')->pluck('id')->toArray();
        if ($request->user()) {
            $adminUserIds[] = $request->user()->id;
        }
        $adminUserIds = array_unique(array_filter($adminUserIds));

        if (!empty($adminUserIds)) {
            $page->users()->syncWithoutDetaching($adminUserIds);
        }

        return back()->with('success', 'Page créée avec succès.');
    }

    public function updatePage(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'route' => ['required', 'string', 'min:2', 'max:100', Rule::unique('pages', 'route')->ignore($page->id), 'regex:/^[a-zA-Z0-9\.\_\-]+$/'],
        ]);

        $page->update($data);

        return back()->with('success', 'Page mise à jour.');
    }

    public function destroyPage(Page $page): RedirectResponse
    {
        $page->delete();

        return back()->with('success', 'Page supprimée avec succès.');
    }
}

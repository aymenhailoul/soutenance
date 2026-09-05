<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Site::with('client')->withCount('equipment');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        $sites = $query->orderBy('name')->paginate(15)->withQueryString();
        $clients = Client::orderBy('name')->get();

        return view('sites.index', compact('sites', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'code' => ['nullable', 'string', 'alpha_dash', 'max:50', 'unique:sites,code'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'regex:/^\+?[0-9\s\-\.\(\)]{8,20}$/'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')->with('success', 'Site créé avec succès.');
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'code' => ['nullable', 'string', 'alpha_dash', 'max:50', 'unique:sites,code,' . $site->id],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'regex:/^\+?[0-9\s\-\.\(\)]{8,20}$/'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $site->update($validated);

        return redirect()->route('sites.index')->with('success', 'Site mis à jour avec succès.');
    }

    public function destroy(Site $site)
    {
        if ($site->equipment()->count() > 0) {
            return redirect()->route('sites.index')->with('error', 'Impossible de supprimer un site auquel des équipements sont associés.');
        }

        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site supprimé avec succès.');
    }
}

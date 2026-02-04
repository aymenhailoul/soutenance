<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Client::with('vehicles');

        // Filter by specific client_id (from dropdown search)
        if ($request->filled('client_id')) {
            $query->where('id', $request->client_id);
        }

        $clients = $query->orderBy('name')->orderBy('prenom')->paginate(15)->withQueryString();

        // Get all clients for the dropdown search (with their vehicles)
        $allClients = Client::with('vehicles:id,client_id,plaque,marque')
            ->select('id', 'name', 'prenom')
            ->orderBy('name')
            ->get();

        return view('clients.index', compact('clients', 'allClients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}


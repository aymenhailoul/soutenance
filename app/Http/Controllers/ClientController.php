<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        return view('clients.index', [
            'clients' => Client::query()->orderBy('name')->orderBy('prenom')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'car_brand' => ['nullable', 'string', 'max:50'],
            'matricule' => ['nullable', 'string', 'max:50'],
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'car_brand' => ['nullable', 'string', 'max:50'],
            'matricule' => ['nullable', 'string', 'max:50'],
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}

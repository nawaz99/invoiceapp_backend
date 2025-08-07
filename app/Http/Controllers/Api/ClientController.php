<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * List only the logged-in user's clients
     */
    public function index()
    {
        return Client::where('user_id', auth()->id())->get();
    }

    /**
     * Store a newly created client for the logged-in user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|size:15'
        ]);

        // Attach logged-in user
        $validated['user_id'] = auth()->id();

        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    /**
     * Show a single client (only if owned by user)
     */
    public function show(Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $client;
    }

    /**
     * Update a client (only if owned by user)
     */
    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email:rfc,dns|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|size:15'
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    /**
     * Delete a client (only if owned by user)
     */
    public function destroy(Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $client->delete();
        return response()->json(['message' => 'Client deleted']);
    }
}

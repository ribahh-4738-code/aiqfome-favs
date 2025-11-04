<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * GET /api/client/{client}
     */
    public function show(Client $client)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // output
        return response()->json($client);
    }

    /**
     * PUT/PATCH /api/client/{client}
     */
    public function update(Request $request, Client $client)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // validade | unique e-mail
        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('client')->ignore($client->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $data = $request->only('name', 'email');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // execute
        $client->update($data);

        // output
        return response()->json($client);
    }

    /**
     * DELETE /api/client/{client}
     */
    public function destroy(Client $client)
    {
        // auth
        if (Auth::id() !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // execute
        $client->delete();

        // output
        return response()->json(null, 204);
    }
}

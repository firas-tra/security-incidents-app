<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return response()->json(Asset::with('tickets')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:server,endpoint,network,application',
            'ip_address' => 'nullable|string',
            'status' => 'required|in:active,inactive,compromised',
        ]);

        $asset = Asset::create($request->all());
        return response()->json($asset, 201);
    }

    public function show(Asset $asset)
    {
        return response()->json($asset->load('tickets'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'type' => 'sometimes|in:server,endpoint,network,application',
            'ip_address' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive,compromised',
        ]);

        $asset->update($request->all());
        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(['message' => 'Asset deleted']);
    }
}
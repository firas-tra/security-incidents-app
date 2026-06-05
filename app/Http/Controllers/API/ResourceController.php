<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::with('category');
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->get('category_id'));
        }
        return response()->json($query->orderBy('name')->get());
    }

    public function show(Resource $resource)
    {
        return response()->json($resource->load('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'category_id' => 'required|exists:categories,id',
            'icon' => 'nullable|string|max:64',
        ]);

        $resource = Resource::create($data);
        return response()->json($resource->load('category'), 201);
    }

    public function update(Request $request, Resource $resource)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'url' => 'sometimes|url',
            'category_id' => 'sometimes|exists:categories,id',
            'icon' => 'nullable|string|max:64',
        ]);

        $resource->update($data);
        return response()->json($resource->load('category'));
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(['message' => 'Resource deleted']);
    }
}

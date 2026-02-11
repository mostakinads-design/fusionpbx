<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    /**
     * List extensions with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $extensions = Extension::with('user')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $extensions,
        ], 200);
    }

    /**
     * Create extension.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'extension_number' => 'required|string|max:255|unique:extensions',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|in:active,inactive,suspended',
            'description' => 'nullable|string',
        ]);

        $extension = Extension::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Extension created successfully',
            'data' => $extension->load('user'),
        ], 201);
    }

    /**
     * Get extension.
     */
    public function show(string $id): JsonResponse
    {
        $extension = Extension::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $extension,
        ], 200);
    }

    /**
     * Update extension.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $extension = Extension::findOrFail($id);

        $validated = $request->validate([
            'extension_number' => 'sometimes|string|max:255|unique:extensions,extension_number,' . $id,
            'user_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|string|in:active,inactive,suspended',
            'description' => 'nullable|string',
        ]);

        $extension->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Extension updated successfully',
            'data' => $extension->fresh()->load('user'),
        ], 200);
    }

    /**
     * Delete extension (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $extension = Extension::findOrFail($id);
        $extension->delete();

        return response()->json([
            'success' => true,
            'message' => 'Extension deleted successfully',
        ], 200);
    }
}

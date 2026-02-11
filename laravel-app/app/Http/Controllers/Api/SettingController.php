<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * List all settings.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Setting::query();

        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        $settings = $query->get();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ], 200);
    }

    /**
     * Create/update setting.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|string|in:string,boolean,integer,json',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (is_array($validated['value'])) {
            $validated['value'] = json_encode($validated['value']);
        }

        $setting = Setting::updateOrCreate(
            ['key' => $validated['key']],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Setting saved successfully',
            'data' => $setting,
        ], 201);
    }

    /**
     * Get setting.
     */
    public function show(string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $setting,
        ], 200);
    }

    /**
     * Update setting.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);

        $validated = $request->validate([
            'key' => 'sometimes|string|max:255',
            'value' => 'sometimes',
            'type' => 'sometimes|string|in:string,boolean,integer,json',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['value']) && is_array($validated['value'])) {
            $validated['value'] = json_encode($validated['value']);
        }

        $setting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting->fresh(),
        ], 200);
    }

    /**
     * Delete setting.
     */
    public function destroy(string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully',
        ], 200);
    }

    /**
     * Get setting by key.
     */
    public function getByKey(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $setting,
        ], 200);
    }
}

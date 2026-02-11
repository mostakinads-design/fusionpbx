<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallLogController extends Controller
{
    /**
     * List call logs with pagination and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $query = CallLog::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('caller_id')) {
            $query->where('caller_id', 'like', '%' . $request->caller_id . '%');
        }

        if ($request->has('call_type')) {
            $query->where('call_type', $request->call_type);
        }

        if ($request->has('from_date')) {
            $query->whereDate('call_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('call_date', '<=', $request->to_date);
        }

        $callLogs = $query->orderBy('call_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $callLogs,
        ], 200);
    }

    /**
     * Create call log.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'caller_id' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'duration' => 'required|integer|min:0',
            'status' => 'required|string|in:completed,missed,failed,busy',
            'call_type' => 'required|string|in:inbound,outbound,internal',
            'call_date' => 'required|date',
        ]);

        $callLog = CallLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Call log created successfully',
            'data' => $callLog,
        ], 201);
    }

    /**
     * Get call log.
     */
    public function show(string $id): JsonResponse
    {
        $callLog = CallLog::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $callLog,
        ], 200);
    }

    /**
     * Update call log.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $callLog = CallLog::findOrFail($id);

        $validated = $request->validate([
            'caller_id' => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
            'duration' => 'sometimes|integer|min:0',
            'status' => 'sometimes|string|in:completed,missed,failed,busy',
            'call_type' => 'sometimes|string|in:inbound,outbound,internal',
            'call_date' => 'sometimes|date',
        ]);

        $callLog->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Call log updated successfully',
            'data' => $callLog->fresh(),
        ], 200);
    }

    /**
     * Delete call log.
     */
    public function destroy(string $id): JsonResponse
    {
        $callLog = CallLog::findOrFail($id);
        $callLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Call log deleted successfully',
        ], 200);
    }

    /**
     * Filter by status.
     */
    public function filterByStatus(Request $request, string $status): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $callLogs = CallLog::where('status', $status)
            ->orderBy('call_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $callLogs,
        ], 200);
    }

    /**
     * Filter by caller.
     */
    public function filterByCaller(Request $request, string $callerId): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $callLogs = CallLog::where('caller_id', 'like', '%' . $callerId . '%')
            ->orderBy('call_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $callLogs,
        ], 200);
    }
}

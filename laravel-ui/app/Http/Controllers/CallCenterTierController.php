<?php

namespace App\Http\Controllers;

use App\Models\CallCenterTier;
use App\Models\CallCenterQueue;
use App\Models\CallCenterAgent;
use Illuminate\Http\Request;

class CallCenterTierController extends Controller
{
    public function index(Request $request)
    {
        $query = CallCenterTier::with(['queue', 'agent']);

        if ($request->has('queue_name')) {
            $query->where('queue_name', $request->queue_name);
        }

        if ($request->has('agent_name')) {
            $query->where('agent_name', $request->agent_name);
        }

        $tiers = $query->paginate(15);
        $queues = CallCenterQueue::all();
        $agents = CallCenterAgent::all();

        return view('call-center-tiers.index', compact('tiers', 'queues', 'agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'agent_name' => 'required|string|max:255',
            'queue_name' => 'required|string|max:255',
            'tier_level' => 'required|integer|min:1',
            'tier_position' => 'required|integer|min:1',
        ]);

        $validated['insert_user'] = auth()->user()->username ?? 'system';

        CallCenterTier::create($validated);

        return redirect()->route('call-center-tiers.index')
            ->withSuccess('Call center tier created successfully.');
    }

    public function update(Request $request, CallCenterTier $callCenterTier)
    {
        $validated = $request->validate([
            'tier_level' => 'required|integer|min:1',
            'tier_position' => 'required|integer|min:1',
        ]);

        $validated['update_user'] = auth()->user()->username ?? 'system';

        $callCenterTier->update($validated);

        return redirect()->route('call-center-tiers.index')
            ->withSuccess('Call center tier updated successfully.');
    }

    public function destroy(CallCenterTier $callCenterTier)
    {
        $callCenterTier->delete();

        return redirect()->route('call-center-tiers.index')
            ->withSuccess('Call center tier deleted successfully.');
    }
}

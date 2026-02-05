<?php

namespace App\Http\Controllers;

use App\Models\CallCenterQueue;
use App\Models\Domain;
use Illuminate\Http\Request;

class CallCenterQueueController extends Controller
{
    public function index(Request $request)
    {
        $query = CallCenterQueue::with('domain');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('queue_name', 'like', "%{$search}%")
                  ->orWhere('queue_extension', 'like', "%{$search}%");
            });
        }

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $queues = $query->paginate(15);

        return view('call-center-queues.index', compact('queues'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('call-center-queues.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'queue_name' => 'required|string|max:255',
            'queue_extension' => 'required|string|max:255',
            'queue_strategy' => 'nullable|string|max:255',
            'queue_moh_sound' => 'nullable|string|max:255',
            'queue_time_base_score' => 'nullable|string|max:255',
            'queue_max_wait_time' => 'nullable|integer',
            'queue_max_wait_time_with_no_agent' => 'nullable|integer',
            'queue_tier_rules_apply' => 'boolean',
            'queue_tier_rule_wait_second' => 'nullable|integer',
            'queue_tier_rule_wait_multiply_level' => 'nullable|boolean',
            'queue_tier_rule_no_agent_no_wait' => 'nullable|boolean',
            'queue_discard_abandoned_after' => 'nullable|integer',
            'queue_abandoned_resume_allowed' => 'boolean',
            'queue_cid_prefix' => 'nullable|string|max:255',
            'queue_announce_position' => 'nullable|string|max:255',
            'queue_announce_sound' => 'nullable|string|max:255',
            'queue_announce_frequency' => 'nullable|integer',
            'queue_description' => 'nullable|string',
            'queue_enabled' => 'boolean',
        ]);

        $validated['insert_user'] = auth()->user()->username ?? 'system';

        CallCenterQueue::create($validated);

        return redirect()->route('call-center-queues.index')
            ->withSuccess('Call center queue created successfully.');
    }

    public function show(CallCenterQueue $callCenterQueue)
    {
        $callCenterQueue->load(['domain', 'tiers.agent']);
        return view('call-center-queues.show', compact('callCenterQueue'));
    }

    public function edit(CallCenterQueue $callCenterQueue)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('call-center-queues.edit', compact('callCenterQueue', 'domains'));
    }

    public function update(Request $request, CallCenterQueue $callCenterQueue)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'queue_name' => 'required|string|max:255',
            'queue_extension' => 'required|string|max:255',
            'queue_strategy' => 'nullable|string|max:255',
            'queue_moh_sound' => 'nullable|string|max:255',
            'queue_time_base_score' => 'nullable|string|max:255',
            'queue_max_wait_time' => 'nullable|integer',
            'queue_max_wait_time_with_no_agent' => 'nullable|integer',
            'queue_tier_rules_apply' => 'boolean',
            'queue_tier_rule_wait_second' => 'nullable|integer',
            'queue_tier_rule_wait_multiply_level' => 'nullable|boolean',
            'queue_tier_rule_no_agent_no_wait' => 'nullable|boolean',
            'queue_discard_abandoned_after' => 'nullable|integer',
            'queue_abandoned_resume_allowed' => 'boolean',
            'queue_cid_prefix' => 'nullable|string|max:255',
            'queue_announce_position' => 'nullable|string|max:255',
            'queue_announce_sound' => 'nullable|string|max:255',
            'queue_announce_frequency' => 'nullable|integer',
            'queue_description' => 'nullable|string',
            'queue_enabled' => 'boolean',
        ]);

        $validated['update_user'] = auth()->user()->username ?? 'system';

        $callCenterQueue->update($validated);

        return redirect()->route('call-center-queues.index')
            ->withSuccess('Call center queue updated successfully.');
    }

    public function destroy(CallCenterQueue $callCenterQueue)
    {
        $callCenterQueue->delete();

        return redirect()->route('call-center-queues.index')
            ->withSuccess('Call center queue deleted successfully.');
    }
}

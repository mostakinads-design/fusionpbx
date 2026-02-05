<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Domain;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with('domain');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('campaign_name', 'like', "%{$search}%")
                  ->orWhere('campaign_type', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $campaigns = $query->paginate(15);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('campaigns.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|string|in:predictive,progressive,preview,manual',
            'broadcast_type' => 'nullable|string|in:voice,sms,voice_sms',
            'ai_enabled' => 'boolean',
            'ai_provider' => 'nullable|string|max:255',
            'ai_model' => 'nullable|string|max:255',
            'ai_prompt' => 'nullable|string',
            'voice_message' => 'nullable|string',
            'sms_message' => 'nullable|string',
            'caller_id_number' => 'nullable|string|max:255',
            'caller_id_name' => 'nullable|string|max:255',
            'scheduled_start' => 'nullable|date',
            'max_retry_attempts' => 'nullable|integer|min:0',
        ]);

        $validated['status'] = 'draft';
        $validated['insert_user'] = optional(auth()->user())->username ?? 'system';

        Campaign::create($validated);

        return redirect()->route('campaigns.index')
            ->withSuccess('Campaign created successfully.');
    }

    public function edit(Campaign $campaign)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('campaigns.edit', compact('campaign', 'domains'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|string|in:predictive,progressive,preview,manual',
            'broadcast_type' => 'nullable|string|in:voice,sms,voice_sms',
            'ai_enabled' => 'boolean',
            'ai_provider' => 'nullable|string|max:255',
            'ai_model' => 'nullable|string|max:255',
            'ai_prompt' => 'nullable|string',
            'voice_message' => 'nullable|string',
            'sms_message' => 'nullable|string',
            'caller_id_number' => 'nullable|string|max:255',
            'caller_id_name' => 'nullable|string|max:255',
            'scheduled_start' => 'nullable|date',
            'max_retry_attempts' => 'nullable|integer|min:0',
        ]);

        $validated['update_user'] = optional(auth()->user())->username ?? 'system';

        $campaign->update($validated);

        return redirect()->route('campaigns.index')
            ->withSuccess('Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->withSuccess('Campaign deleted successfully.');
    }

    public function start(Campaign $campaign)
    {
        if ($campaign->status === 'running') {
            return redirect()->back()
                ->withErrors('Campaign is already running.');
        }

        $campaign->update([
            'status' => 'running',
            'update_user' => optional(auth()->user())->username ?? 'system',
        ]);

        return redirect()->back()
            ->withSuccess('Campaign started successfully.');
    }

    public function pause(Campaign $campaign)
    {
        if ($campaign->status !== 'running') {
            return redirect()->back()
                ->withErrors('Campaign must be running to pause.');
        }

        $campaign->update([
            'status' => 'paused',
            'update_user' => optional(auth()->user())->username ?? 'system',
        ]);

        return redirect()->back()
            ->withSuccess('Campaign paused successfully.');
    }

    public function stop(Campaign $campaign)
    {
        if ($campaign->status === 'completed') {
            return redirect()->back()
                ->withErrors('Campaign is already completed.');
        }

        $campaign->update([
            'status' => 'completed',
            'update_user' => optional(auth()->user())->username ?? 'system',
        ]);

        return redirect()->back()
            ->withSuccess('Campaign stopped successfully.');
    }
}

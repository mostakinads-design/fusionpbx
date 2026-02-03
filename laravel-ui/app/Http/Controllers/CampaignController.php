<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with('domain');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        if ($request->has('status')) {
            $query->where('campaign_status', $request->status);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);
        $domains = Domain::all();
        
        return view('campaigns.index', compact('campaigns', 'domains'));
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
            'campaign_description' => 'nullable|string',
            'campaign_type' => 'required|in:predictive,progressive,preview,manual',
            'dialing_mode' => 'required|in:power,predictive,preview',
            'max_attempts' => 'required|integer|min:1|max:10',
            'retry_delay' => 'required|integer|min:60',
            'call_timeout' => 'required|integer|min:10|max:120',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'ai_agent_enabled' => 'boolean',
            'ai_agent_config' => 'nullable|array',
            'caller_id_name' => 'nullable|string|max:255',
            'caller_id_number' => 'nullable|string|max:255',
        ]);

        $validated['campaign_uuid'] = (string) Str::uuid();
        $validated['ai_agent_enabled'] = $request->has('ai_agent_enabled');
        $validated['campaign_status'] = 'draft';
        
        Campaign::create($validated);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('domain', 'contacts', 'calls', 'agents');
        
        $stats = [
            'total_contacts' => $campaign->contacts()->count(),
            'pending_contacts' => $campaign->contacts()->where('status', 'new')->count(),
            'completed_contacts' => $campaign->contacts()->where('status', 'completed')->count(),
            'total_calls' => $campaign->calls()->count(),
            'answered_calls' => $campaign->calls()->where('call_status', 'answered')->count(),
            'ai_handled_calls' => $campaign->calls()->where('is_ai_handled', true)->count(),
        ];
        
        return view('campaigns.show', compact('campaign', 'stats'));
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
            'campaign_description' => 'nullable|string',
            'campaign_type' => 'required|in:predictive,progressive,preview,manual',
            'dialing_mode' => 'required|in:power,predictive,preview',
            'max_attempts' => 'required|integer|min:1|max:10',
            'retry_delay' => 'required|integer|min:60',
            'call_timeout' => 'required|integer|min:10|max:120',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'ai_agent_enabled' => 'boolean',
            'ai_agent_config' => 'nullable|array',
            'caller_id_name' => 'nullable|string|max:255',
            'caller_id_number' => 'nullable|string|max:255',
            'campaign_status' => 'required|in:draft,active,paused,completed,archived',
        ]);

        $validated['ai_agent_enabled'] = $request->has('ai_agent_enabled');
        
        $campaign->update($validated);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function start(Campaign $campaign)
    {
        $campaign->update(['campaign_status' => 'active']);
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign started successfully.');
    }

    public function pause(Campaign $campaign)
    {
        $campaign->update(['campaign_status' => 'paused']);
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign paused successfully.');
    }

    public function resume(Campaign $campaign)
    {
        $campaign->update(['campaign_status' => 'active']);
        
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign resumed successfully.');
    }
}

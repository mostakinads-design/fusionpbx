<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Campaign;
use App\Models\CampaignCall;
use App\Models\CampaignContact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DialerController extends Controller
{
    /**
     * Show the dialer dashboard
     */
    public function index(Request $request)
    {
        $agent = null;
        $campaigns = [];
        $nextContact = null;

        if ($request->has('agent_uuid')) {
            $agent = Agent::with('campaigns')->findOrFail($request->agent_uuid);
            $campaigns = $agent->campaigns()
                ->where('campaign_status', 'active')
                ->get();
                
            // Get next contact to dial
            if ($campaigns->isNotEmpty()) {
                $campaignIds = $campaigns->pluck('campaign_uuid');
                $nextContact = CampaignContact::whereIn('campaign_uuid', $campaignIds)
                    ->where('status', 'new')
                    ->whereNull('next_call_at')
                    ->orWhere('next_call_at', '<=', now())
                    ->orderBy('priority', 'desc')
                    ->first();
            }
        }

        $agents = Agent::where('is_available', true)->get();
        
        return view('dialer.index', compact('agent', 'agents', 'campaigns', 'nextContact'));
    }

    /**
     * Initiate a call
     */
    public function dial(Request $request)
    {
        $validated = $request->validate([
            'campaign_uuid' => 'required|exists:campaigns,campaign_uuid',
            'contact_uuid' => 'required|exists:campaign_contacts,contact_uuid',
            'agent_uuid' => 'nullable|exists:agents,agent_uuid',
        ]);

        $contact = CampaignContact::findOrFail($validated['contact_uuid']);
        $campaign = Campaign::findOrFail($validated['campaign_uuid']);
        
        // Create call record
        $call = CampaignCall::create([
            'call_uuid' => (string) Str::uuid(),
            'campaign_uuid' => $campaign->campaign_uuid,
            'contact_uuid' => $contact->contact_uuid,
            'agent_uuid' => $validated['agent_uuid'] ?? null,
            'phone_number' => $contact->phone_number,
            'call_status' => 'initiated',
            'is_ai_handled' => $campaign->ai_agent_enabled && !isset($validated['agent_uuid']),
        ]);

        // Update contact status
        $contact->update([
            'status' => 'dialing',
            'attempts' => $contact->attempts + 1,
            'last_call_at' => now(),
        ]);

        // Here you would integrate with FreeSWITCH/Asterisk to actually initiate the call
        // For now, we'll just return the call data

        return response()->json([
            'success' => true,
            'call' => $call,
            'contact' => $contact,
            'message' => 'Call initiated successfully'
        ]);
    }

    /**
     * Update call status
     */
    public function updateCallStatus(Request $request, CampaignCall $call)
    {
        $validated = $request->validate([
            'call_status' => 'required|in:initiated,ringing,answered,completed,failed,busy,no_answer',
            'disposition' => 'nullable|in:answer,no_answer,busy,failed,callback,interested,not_interested,do_not_call',
            'notes' => 'nullable|string',
            'call_duration' => 'nullable|integer',
        ]);

        $call->update($validated);

        // Update contact status based on call outcome
        if (isset($validated['disposition'])) {
            $contactStatus = match($validated['disposition']) {
                'interested', 'not_interested' => 'completed',
                'do_not_call' => 'do_not_call',
                'callback' => 'new',
                default => $call->contact->status
            };

            $call->contact->update(['status' => $contactStatus]);
        }

        return response()->json([
            'success' => true,
            'call' => $call
        ]);
    }

    /**
     * Show live agent dashboard
     */
    public function agentDashboard(Agent $agent)
    {
        $activeCampaigns = $agent->campaigns()
            ->where('campaign_status', 'active')
            ->with('contacts')
            ->get();

        $recentCalls = CampaignCall::where('agent_uuid', $agent->agent_uuid)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'calls_today' => CampaignCall::where('agent_uuid', $agent->agent_uuid)
                ->whereDate('created_at', today())
                ->count(),
            'answered_today' => CampaignCall::where('agent_uuid', $agent->agent_uuid)
                ->whereDate('created_at', today())
                ->where('call_status', 'answered')
                ->count(),
            'talk_time_today' => CampaignCall::where('agent_uuid', $agent->agent_uuid)
                ->whereDate('created_at', today())
                ->sum('call_duration'),
        ];

        return view('dialer.agent-dashboard', compact('agent', 'activeCampaigns', 'recentCalls', 'stats'));
    }

    /**
     * Get next contact to dial for agent
     */
    public function getNextContact(Request $request)
    {
        $validated = $request->validate([
            'agent_uuid' => 'required|exists:agents,agent_uuid',
        ]);

        $agent = Agent::findOrFail($validated['agent_uuid']);
        $campaignIds = $agent->campaigns()
            ->where('campaign_status', 'active')
            ->pluck('campaign_uuid');

        $nextContact = CampaignContact::with('campaign')
            ->whereIn('campaign_uuid', $campaignIds)
            ->where('status', 'new')
            ->where(function($query) {
                $query->whereNull('next_call_at')
                    ->orWhere('next_call_at', '<=', now());
            })
            ->orderBy('priority', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'contact' => $nextContact
        ]);
    }
}

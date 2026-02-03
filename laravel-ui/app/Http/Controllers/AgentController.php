<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Domain;
use App\Models\FusionUser;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::with('domain', 'user', 'extension');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        if ($request->has('agent_type')) {
            $query->where('agent_type', $request->agent_type);
        }

        if ($request->has('agent_status')) {
            $query->where('agent_status', $request->agent_status);
        }

        $agents = $query->paginate(15);
        $domains = Domain::all();
        
        return view('agents.index', compact('agents', 'domains'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        $users = FusionUser::where('user_enabled', true)->get();
        $extensions = Extension::where('enabled', true)->get();
        
        return view('agents.create', compact('domains', 'users', 'extensions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'agent_name' => 'required|string|max:255',
            'agent_type' => 'required|in:human,ai',
            'user_uuid' => 'nullable|exists:v_users,user_uuid',
            'extension_uuid' => 'nullable|exists:v_extensions,extension_uuid',
            'max_concurrent_calls' => 'required|integer|min:1|max:10',
            'skills' => 'nullable|array',
            'is_available' => 'boolean',
        ]);

        $validated['agent_uuid'] = (string) Str::uuid();
        $validated['is_available'] = $request->has('is_available');
        $validated['agent_status'] = 'offline';
        
        Agent::create($validated);

        return redirect()->route('agents.index')
            ->with('success', 'Agent created successfully.');
    }

    public function show(Agent $agent)
    {
        $agent->load('domain', 'user', 'extension', 'campaigns', 'calls');
        
        $stats = [
            'total_calls' => $agent->calls()->count(),
            'answered_calls' => $agent->calls()->where('call_status', 'answered')->count(),
            'total_talk_time' => $agent->calls()->sum('call_duration'),
            'active_campaigns' => $agent->campaigns()->where('campaign_status', 'active')->count(),
        ];
        
        return view('agents.show', compact('agent', 'stats'));
    }

    public function edit(Agent $agent)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        $users = FusionUser::where('user_enabled', true)->get();
        $extensions = Extension::where('enabled', true)->get();
        
        return view('agents.edit', compact('agent', 'domains', 'users', 'extensions'));
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'agent_name' => 'required|string|max:255',
            'agent_type' => 'required|in:human,ai',
            'user_uuid' => 'nullable|exists:v_users,user_uuid',
            'extension_uuid' => 'nullable|exists:v_extensions,extension_uuid',
            'max_concurrent_calls' => 'required|integer|min:1|max:10',
            'skills' => 'nullable|array',
            'is_available' => 'boolean',
            'agent_status' => 'required|in:available,on_call,break,offline',
        ]);

        $validated['is_available'] = $request->has('is_available');
        
        $agent->update($validated);

        return redirect()->route('agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();

        return redirect()->route('agents.index')
            ->with('success', 'Agent deleted successfully.');
    }

    public function updateStatus(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'agent_status' => 'required|in:available,on_call,break,offline',
        ]);

        $agent->update($validated);

        return redirect()->back()
            ->with('success', 'Agent status updated successfully.');
    }
}

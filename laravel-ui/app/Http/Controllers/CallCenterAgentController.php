<?php

namespace App\Http\Controllers;

use App\Models\CallCenterAgent;
use App\Models\Domain;
use Illuminate\Http\Request;

class CallCenterAgentController extends Controller
{
    public function index(Request $request)
    {
        $query = CallCenterAgent::with('domain');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('agent_name', 'like', "%{$search}%")
                  ->orWhere('agent_contact', 'like', "%{$search}%");
            });
        }

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        if ($request->has('status')) {
            $query->where('agent_status', $request->status);
        }

        $agents = $query->paginate(15);

        return view('call-center-agents.index', compact('agents'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('call-center-agents.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'agent_name' => 'required|string|max:255',
            'agent_type' => 'nullable|string|max:255',
            'agent_call_timeout' => 'nullable|integer',
            'agent_contact' => 'nullable|string|max:255',
            'agent_status' => 'nullable|string|max:255',
            'agent_no_answer_delay_time' => 'nullable|integer',
            'agent_max_no_answer' => 'nullable|integer',
            'agent_wrap_up_time' => 'nullable|integer',
            'agent_reject_delay_time' => 'nullable|integer',
            'agent_busy_delay_time' => 'nullable|integer',
            'agent_logout_on_reject' => 'boolean',
            'agent_enabled' => 'boolean',
        ]);

        $validated['insert_user'] = auth()->user()->username ?? 'system';

        CallCenterAgent::create($validated);

        return redirect()->route('call-center-agents.index')
            ->withSuccess('Call center agent created successfully.');
    }

    public function edit(CallCenterAgent $callCenterAgent)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('call-center-agents.edit', compact('callCenterAgent', 'domains'));
    }

    public function update(Request $request, CallCenterAgent $callCenterAgent)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'agent_name' => 'required|string|max:255',
            'agent_type' => 'nullable|string|max:255',
            'agent_call_timeout' => 'nullable|integer',
            'agent_contact' => 'nullable|string|max:255',
            'agent_status' => 'nullable|string|max:255',
            'agent_no_answer_delay_time' => 'nullable|integer',
            'agent_max_no_answer' => 'nullable|integer',
            'agent_wrap_up_time' => 'nullable|integer',
            'agent_reject_delay_time' => 'nullable|integer',
            'agent_busy_delay_time' => 'nullable|integer',
            'agent_logout_on_reject' => 'boolean',
            'agent_enabled' => 'boolean',
        ]);

        $validated['update_user'] = auth()->user()->username ?? 'system';

        $callCenterAgent->update($validated);

        return redirect()->route('call-center-agents.index')
            ->withSuccess('Call center agent updated successfully.');
    }

    public function destroy(CallCenterAgent $callCenterAgent)
    {
        $callCenterAgent->delete();

        return redirect()->route('call-center-agents.index')
            ->withSuccess('Call center agent deleted successfully.');
    }

    public function updateStatus(Request $request, CallCenterAgent $callCenterAgent)
    {
        $validated = $request->validate([
            'agent_status' => 'required|string|in:Logged Out,Available,Available (On Demand),On Break',
        ]);

        $callCenterAgent->update([
            'agent_status' => $validated['agent_status'],
            'update_user' => auth()->user()->username ?? 'system',
        ]);

        return redirect()->back()
            ->withSuccess('Agent status updated successfully.');
    }
}

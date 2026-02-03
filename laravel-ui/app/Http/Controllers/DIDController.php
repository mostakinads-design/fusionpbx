<?php

namespace App\Http\Controllers;

use App\Models\DID;
use App\Models\Domain;
use App\Models\Extension;
use App\Models\IVR;
use App\Models\CallQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DIDController extends Controller
{
    /**
     * Display a listing of DIDs
     */
    public function index(Request $request)
    {
        $query = DID::with('domain', 'extension', 'ivr', 'queue');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        if ($request->has('routing_type')) {
            $query->where('routing_type', $request->routing_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $dids = $query->orderBy('did_number')->paginate(25);
        $domains = Domain::all();
        
        return view('dids.index', compact('dids', 'domains'));
    }

    /**
     * Show the form for creating a new DID
     */
    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        $extensions = Extension::where('enabled', true)->get();
        $ivrs = IVR::where('is_active', true)->get();
        $queues = CallQueue::where('is_active', true)->get();
        
        return view('dids.create', compact('domains', 'extensions', 'ivrs', 'queues'));
    }

    /**
     * Store a newly created DID
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'did_number' => 'required|string|unique:dids,did_number',
            'did_description' => 'nullable|string',
            'destination_type' => 'required|in:extension,ivr,queue,external,voicemail',
            'destination_number' => 'required|string',
            'extension_uuid' => 'nullable|exists:v_extensions,extension_uuid',
            'ivr_uuid' => 'nullable|exists:ivr_menus,ivr_uuid',
            'queue_uuid' => 'nullable|exists:call_queues,queue_uuid',
            'routing_type' => 'required|in:voice_only,sms_only,voice_and_sms',
            'country_code' => 'nullable|string|max:5',
            'area_code' => 'nullable|string|max:10',
            'caller_id_name' => 'nullable|string|max:255',
            'caller_id_number' => 'nullable|string|max:255',
            'record_calls' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['did_uuid'] = (string) Str::uuid();
        
        // Set voice_enabled and sms_enabled based on routing_type
        $validated['voice_enabled'] = in_array($validated['routing_type'], ['voice_only', 'voice_and_sms']);
        $validated['sms_enabled'] = in_array($validated['routing_type'], ['sms_only', 'voice_and_sms']);
        $validated['record_calls'] = $request->has('record_calls');
        $validated['is_active'] = $request->has('is_active');
        
        DID::create($validated);

        return redirect()->route('dids.index')
            ->with('success', 'DID created successfully.');
    }

    /**
     * Display the specified DID
     */
    public function show(DID $did)
    {
        $did->load('domain', 'extension', 'ivr', 'queue', 'callRecords');
        
        $stats = [
            'total_calls' => $did->callRecords()->count(),
            'calls_today' => $did->callRecords()->whereDate('start_stamp', today())->count(),
            'calls_this_month' => $did->callRecords()
                ->whereBetween('start_stamp', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'avg_duration' => $did->callRecords()->avg('duration'),
        ];
        
        return view('dids.show', compact('did', 'stats'));
    }

    /**
     * Show the form for editing the specified DID
     */
    public function edit(DID $did)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        $extensions = Extension::where('enabled', true)->get();
        $ivrs = IVR::where('is_active', true)->get();
        $queues = CallQueue::where('is_active', true)->get();
        
        return view('dids.edit', compact('did', 'domains', 'extensions', 'ivrs', 'queues'));
    }

    /**
     * Update the specified DID
     */
    public function update(Request $request, DID $did)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'did_number' => 'required|string|unique:dids,did_number,' . $did->did_uuid . ',did_uuid',
            'did_description' => 'nullable|string',
            'destination_type' => 'required|in:extension,ivr,queue,external,voicemail',
            'destination_number' => 'required|string',
            'extension_uuid' => 'nullable|exists:v_extensions,extension_uuid',
            'ivr_uuid' => 'nullable|exists:ivr_menus,ivr_uuid',
            'queue_uuid' => 'nullable|exists:call_queues,queue_uuid',
            'routing_type' => 'required|in:voice_only,sms_only,voice_and_sms',
            'country_code' => 'nullable|string|max:5',
            'area_code' => 'nullable|string|max:10',
            'caller_id_name' => 'nullable|string|max:255',
            'caller_id_number' => 'nullable|string|max:255',
            'record_calls' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Set voice_enabled and sms_enabled based on routing_type
        $validated['voice_enabled'] = in_array($validated['routing_type'], ['voice_only', 'voice_and_sms']);
        $validated['sms_enabled'] = in_array($validated['routing_type'], ['sms_only', 'voice_and_sms']);
        $validated['record_calls'] = $request->has('record_calls');
        $validated['is_active'] = $request->has('is_active');
        
        $did->update($validated);

        return redirect()->route('dids.index')
            ->with('success', 'DID updated successfully.');
    }

    /**
     * Remove the specified DID
     */
    public function destroy(DID $did)
    {
        $did->delete();

        return redirect()->route('dids.index')
            ->with('success', 'DID deleted successfully.');
    }

    /**
     * Toggle DID active status
     */
    public function toggleStatus(DID $did)
    {
        $did->update(['is_active' => !$did->is_active]);

        return redirect()->back()
            ->with('success', 'DID status updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Extension;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExtensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Extension::with('domain');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $extensions = $query->paginate(15);
        $domains = Domain::all();
        
        return view('extensions.index', compact('extensions', 'domains'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('extensions.create', compact('domains'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'extension' => 'required|string|max:255',
            'number_alias' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'effective_caller_id_name' => 'nullable|string|max:255',
            'effective_caller_id_number' => 'nullable|string|max:255',
            'user_context' => 'nullable|string|max:255',
            'call_timeout' => 'nullable|integer',
            'enabled' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['extension_uuid'] = (string) Str::uuid();
        $validated['enabled'] = $request->has('enabled');
        
        Extension::create($validated);

        return redirect()->route('extensions.index')
            ->with('success', 'Extension created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Extension $extension)
    {
        $extension->load('domain', 'callRecords');
        return view('extensions.show', compact('extension'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Extension $extension)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('extensions.edit', compact('extension', 'domains'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Extension $extension)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'extension' => 'required|string|max:255',
            'number_alias' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'effective_caller_id_name' => 'nullable|string|max:255',
            'effective_caller_id_number' => 'nullable|string|max:255',
            'user_context' => 'nullable|string|max:255',
            'call_timeout' => 'nullable|integer',
            'enabled' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['enabled'] = $request->has('enabled');
        
        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        
        $extension->update($validated);

        return redirect()->route('extensions.index')
            ->with('success', 'Extension updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Extension $extension)
    {
        $extension->delete();

        return redirect()->route('extensions.index')
            ->with('success', 'Extension deleted successfully.');
    }
}

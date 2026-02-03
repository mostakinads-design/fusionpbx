<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domains = Domain::with('users', 'extensions')
            ->paginate(15);
        
        return view('domains.index', compact('domains'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('domains.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:v_domains',
            'domain_enabled' => 'boolean',
            'domain_description' => 'nullable|string',
        ]);

        $validated['domain_uuid'] = (string) Str::uuid();
        $validated['domain_enabled'] = $request->has('domain_enabled');
        
        Domain::create($validated);

        return redirect()->route('domains.index')
            ->with('success', 'Domain created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain)
    {
        $domain->load('users', 'extensions', 'callRecords');
        return view('domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Domain $domain)
    {
        return view('domains.edit', compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Domain $domain)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:v_domains,domain_name,' . $domain->domain_uuid . ',domain_uuid',
            'domain_enabled' => 'boolean',
            'domain_description' => 'nullable|string',
        ]);

        $validated['domain_enabled'] = $request->has('domain_enabled');
        
        $domain->update($validated);

        return redirect()->route('domains.index')
            ->with('success', 'Domain updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('domains.index')
            ->with('success', 'Domain deleted successfully.');
    }
}

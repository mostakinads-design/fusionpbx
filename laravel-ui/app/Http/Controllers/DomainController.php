<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $query = Domain::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('domain_name', 'like', "%{$search}%")
                  ->orWhere('domain_description', 'like', "%{$search}%");
            });
        }

        $domains = $query->paginate(15);

        return view('domains.index', compact('domains'));
    }

    public function create()
    {
        return view('domains.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:v_domains,domain_name',
            'domain_enabled' => 'boolean',
            'domain_description' => 'nullable|string',
        ]);

        $validated['insert_user'] = auth()->user()->username ?? 'system';

        Domain::create($validated);

        return redirect()->route('domains.index')
            ->withSuccess('Domain created successfully.');
    }

    public function edit(Domain $domain)
    {
        return view('domains.edit', compact('domain'));
    }

    public function update(Request $request, Domain $domain)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:v_domains,domain_name,' . $domain->domain_uuid . ',domain_uuid',
            'domain_enabled' => 'boolean',
            'domain_description' => 'nullable|string',
        ]);

        $validated['update_user'] = auth()->user()->username ?? 'system';

        $domain->update($validated);

        return redirect()->route('domains.index')
            ->withSuccess('Domain updated successfully.');
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('domains.index')
            ->withSuccess('Domain deleted successfully.');
    }
}

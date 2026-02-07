<?php

namespace App\Http\Controllers;

use App\Models\Extension;
use App\Models\Domain;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index(Request $request)
    {
        $query = Extension::with('domain');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('extension', 'like', "%{$search}%")
                  ->orWhere('directory_first_name', 'like', "%{$search}%")
                  ->orWhere('directory_last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $extensions = $query->paginate(15);

        return view('extensions.index', compact('extensions'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('extensions.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'extension' => 'required|string|max:255',
            'number_alias' => 'nullable|string|max:255',
            'password' => 'required|string|max:255',
            'accountcode' => 'nullable|string|max:255',
            'effective_caller_id_name' => 'nullable|string|max:255',
            'effective_caller_id_number' => 'nullable|string|max:255',
            'outbound_caller_id_name' => 'nullable|string|max:255',
            'outbound_caller_id_number' => 'nullable|string|max:255',
            'directory_first_name' => 'nullable|string|max:255',
            'directory_last_name' => 'nullable|string|max:255',
            'directory_visible' => 'boolean',
            'directory_exten_visible' => 'boolean',
            'user_context' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['insert_user'] = optional(auth()->user())->username ?? 'system';

        Extension::create($validated);

        return redirect()->route('extensions.index')
            ->withSuccess('Extension created successfully.');
    }

    public function edit(Extension $extension)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('extensions.edit', compact('extension', 'domains'));
    }

    public function update(Request $request, Extension $extension)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'extension' => 'required|string|max:255',
            'number_alias' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'accountcode' => 'nullable|string|max:255',
            'effective_caller_id_name' => 'nullable|string|max:255',
            'effective_caller_id_number' => 'nullable|string|max:255',
            'outbound_caller_id_name' => 'nullable|string|max:255',
            'outbound_caller_id_number' => 'nullable|string|max:255',
            'directory_first_name' => 'nullable|string|max:255',
            'directory_last_name' => 'nullable|string|max:255',
            'directory_visible' => 'boolean',
            'directory_exten_visible' => 'boolean',
            'user_context' => 'nullable|string|max:255',
            'enabled' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['update_user'] = optional(auth()->user())->username ?? 'system';

        $extension->update($validated);

        return redirect()->route('extensions.index')
            ->withSuccess('Extension updated successfully.');
    }

    public function destroy(Extension $extension)
    {
        $extension->delete();

        return redirect()->route('extensions.index')
            ->withSuccess('Extension deleted successfully.');
    }
}

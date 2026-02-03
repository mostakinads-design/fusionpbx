<?php

namespace App\Http\Controllers;

use App\Models\FusionUser;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class FusionUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FusionUser::with('domain');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $users = $query->paginate(15);
        $domains = Domain::all();
        
        return view('users.index', compact('users', 'domains'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('users.create', compact('domains'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'username' => 'required|string|max:255|unique:v_users',
            'password' => 'required|string|min:8|confirmed',
            'user_email' => 'required|email|max:255',
            'user_enabled' => 'boolean',
        ]);

        $validated['user_uuid'] = (string) Str::uuid();
        $validated['user_enabled'] = $request->has('user_enabled');
        $validated['salt'] = bin2hex(random_bytes(16));
        $validated['password'] = md5($validated['salt'] . $validated['password']);
        
        FusionUser::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FusionUser $user)
    {
        $user->load('domain', 'userGroups');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FusionUser $user)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('users.edit', compact('user', 'domains'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FusionUser $user)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'username' => 'required|string|max:255|unique:v_users,username,' . $user->user_uuid . ',user_uuid',
            'password' => 'nullable|string|min:8|confirmed',
            'user_email' => 'required|email|max:255',
            'user_enabled' => 'boolean',
        ]);

        $validated['user_enabled'] = $request->has('user_enabled');
        
        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['salt'] = bin2hex(random_bytes(16));
            $validated['password'] = md5($validated['salt'] . $validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FusionUser $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}

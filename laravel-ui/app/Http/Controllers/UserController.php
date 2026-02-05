<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('domain');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('users.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'user_email' => 'nullable|email|max:255',
            'user_enabled' => 'boolean',
            'contact_uuid' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['insert_user'] = auth()->user()->username ?? 'system';

        User::create($validated);

        return redirect()->route('users.index')
            ->withSuccess('User created successfully.');
    }

    public function edit(User $user)
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('users.edit', compact('user', 'domains'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'user_email' => 'nullable|email|max:255',
            'user_enabled' => 'boolean',
            'contact_uuid' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['update_user'] = auth()->user()->username ?? 'system';

        $user->update($validated);

        return redirect()->route('users.index')
            ->withSuccess('User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess('User deleted successfully.');
    }
}

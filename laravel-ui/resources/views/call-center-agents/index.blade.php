@extends('layouts.app')

@section('title', 'Call Center Agents')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Call Center Agents</h3>
        <p class="text-gray-600">Manage call center agents</p>
    </div>
    <a href="{{ route('call-center-agents.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
        <i class="fas fa-plus mr-2"></i> Add Agent
    </a>
</div>

<!-- Search -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('call-center-agents.index') }}" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search agents by name or extension..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">All Statuses</option>
            <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>Available</option>
            <option value="On Break" {{ request('status') == 'On Break' ? 'selected' : '' }}>On Break</option>
            <option value="Logged Out" {{ request('status') == 'Logged Out' ? 'selected' : '' }}>Logged Out</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i> Search
        </button>
        @if(request('search') || request('status'))
        <a href="{{ route('call-center-agents.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
            <i class="fas fa-times mr-2"></i> Clear
        </a>
        @endif
    </form>
</div>

<!-- Agents Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extension</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queues</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max No Answer</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($agents as $agent)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center">
                            <i class="fas fa-headset text-teal-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $agent->name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $agent->extension ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->type ?? 'callback' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        @php
                            $statusColors = [
                                'Available' => 'green',
                                'On Break' => 'yellow',
                                'Logged Out' => 'red',
                                'Do Not Disturb' => 'red',
                            ];
                            $color = $statusColors[$agent->status ?? 'Logged Out'] ?? 'gray';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                            {{ $agent->status ?? 'Logged Out' }}
                        </span>
                        <form action="{{ route('call-center-agents.update-status', $agent->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-xs border-gray-300 rounded">
                                <option value="Available" {{ ($agent->status ?? '') == 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="On Break" {{ ($agent->status ?? '') == 'On Break' ? 'selected' : '' }}>On Break</option>
                                <option value="Do Not Disturb" {{ ($agent->status ?? '') == 'Do Not Disturb' ? 'selected' : '' }}>DND</option>
                                <option value="Logged Out" {{ ($agent->status ?? '') == 'Logged Out' ? 'selected' : '' }}>Logged Out</option>
                            </select>
                        </form>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->queues_count ?? 0 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->max_no_answer ?? 3 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('call-center-agents.edit', $agent->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('call-center-agents.destroy', $agent->id) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this agent?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-headset text-4xl mb-4 text-gray-400"></i>
                    <p class="text-lg">No agents found</p>
                    <a href="{{ route('call-center-agents.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Create your first agent
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($agents->hasPages())
<div class="mt-6">
    {{ $agents->links() }}
</div>
@endif
@endsection

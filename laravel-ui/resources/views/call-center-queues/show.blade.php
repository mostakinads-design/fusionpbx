@extends('layouts.app')

@section('title', 'Queue Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('call-center-queues.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Queues
    </a>
</div>

<!-- Queue Information -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $queue->name }}</h3>
            <p class="text-gray-600 mt-1">{{ $queue->description }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('call-center-queues.edit', $queue->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Queue
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-1">Extension</p>
            <p class="text-xl font-semibold text-gray-800">{{ $queue->extension }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Strategy</p>
            <p class="text-xl font-semibold text-gray-800">{{ $queue->strategy }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Status</p>
            @if($queue->enabled ?? true)
            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                Active
            </span>
            @else
            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                Inactive
            </span>
            @endif
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Agents</p>
            <p class="text-xl font-semibold text-gray-800">{{ $agents->count() }}</p>
        </div>
    </div>
</div>

<!-- Queue Settings -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Queue Settings</h4>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-600">Timeout</p>
            <p class="text-lg font-medium text-gray-800">{{ $queue->timeout ?? 'N/A' }}s</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Max Wait Time</p>
            <p class="text-lg font-medium text-gray-800">{{ $queue->max_wait_time ?? 'N/A' }}s</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Tier Rules Apply</p>
            <p class="text-lg font-medium text-gray-800">{{ $queue->tier_rules_apply ?? 'false' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Record Calls</p>
            <p class="text-lg font-medium text-gray-800">{{ ($queue->record ?? false) ? 'Yes' : 'No' }}</p>
        </div>
    </div>
</div>

<!-- Assigned Agents & Tiers -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-lg font-semibold text-gray-800">Assigned Agents</h4>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Agent
        </button>
    </div>

    @if($agents->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extension</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($agents as $agent)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $agent->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $agent->extension ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $agent->status == 'Available' ? 'green' : 'yellow' }}-100 text-{{ $agent->status == 'Available' ? 'green' : 'yellow' }}-800">
                            {{ $agent->status ?? 'Unknown' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $agent->pivot->tier_level ?? 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $agent->pivot->position ?? 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Edit Tier
                        </button>
                        <button class="text-red-600 hover:text-red-900" onclick="return confirm('Remove agent from queue?');">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-headset text-4xl mb-4 text-gray-400"></i>
        <p class="text-gray-500 mb-4">No agents assigned to this queue</p>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Your First Agent
        </button>
    </div>
    @endif
</div>
@endsection

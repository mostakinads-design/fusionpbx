@extends('layouts.app')

@section('title', 'Campaign Dashboard')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('campaigns.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Campaigns
    </a>
    <div class="flex space-x-2">
        @if($campaign->status == 'running')
        <form action="{{ route('campaigns.pause', $campaign->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-pause mr-2"></i> Pause
            </button>
        </form>
        @elseif($campaign->status == 'paused' || $campaign->status == 'stopped')
        <form action="{{ route('campaigns.start', $campaign->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-play mr-2"></i> Start
            </button>
        </form>
        @endif
        
        @if($campaign->status == 'running' || $campaign->status == 'paused')
        <form action="{{ route('campaigns.stop', $campaign->id) }}" method="POST" class="inline" 
              onsubmit="return confirm('Are you sure you want to stop this campaign?');">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-stop mr-2"></i> Stop
            </button>
        </form>
        @endif

        <a href="{{ route('campaigns.edit', $campaign->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
    </div>
</div>

<!-- Campaign Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $campaign->name }}</h3>
            <p class="text-gray-600 mt-2">{{ $campaign->description }}</p>
        </div>
        @php
            $statusColors = [
                'running' => 'green',
                'paused' => 'yellow',
                'stopped' => 'red',
                'completed' => 'gray',
            ];
            $color = $statusColors[$campaign->status ?? 'stopped'] ?? 'gray';
        @endphp
        <span class="px-4 py-2 text-lg font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
            {{ ucfirst($campaign->status ?? 'stopped') }}
        </span>
    </div>
</div>

<!-- Campaign Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Total Contacts</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalContacts ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Completed</p>
        <p class="text-3xl font-bold text-green-600">{{ $completedContacts ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">In Progress</p>
        <p class="text-3xl font-bold text-blue-600">{{ $inProgressContacts ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Success Rate</p>
        @php
            $total = $totalContacts ?? 0;
            $completed = $completedContacts ?? 0;
            $successRate = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp
        <p class="text-3xl font-bold text-purple-600">{{ $successRate }}%</p>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Campaign Progress</h4>
    <div class="w-full bg-gray-200 rounded-full h-4">
        <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" style="width: {{ $successRate }}%"></div>
    </div>
    <p class="text-sm text-gray-600 mt-2">{{ $completedContacts ?? 0 }} of {{ $totalContacts ?? 0 }} contacts completed</p>
</div>

<!-- AI Configuration -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">AI Configuration</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-600">Provider</p>
            <p class="text-lg font-medium text-gray-800">{{ $campaign->ai_provider ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Model</p>
            <p class="text-lg font-medium text-gray-800">{{ $campaign->ai_model ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Voice</p>
            <p class="text-lg font-medium text-gray-800">{{ $campaign->ai_voice ?? 'alloy' }}</p>
        </div>
    </div>
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-2">AI Prompt</p>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $campaign->ai_prompt }}</p>
        </div>
    </div>
</div>

<!-- Contacts -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-lg font-semibold text-gray-800">Campaign Contacts</h4>
        <div class="flex space-x-2">
            <a href="{{ route('campaigns.contacts.import', $campaign->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <i class="fas fa-upload mr-2"></i> Import Contacts
            </a>
            <a href="{{ route('campaigns.contacts.index', $campaign->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <i class="fas fa-list mr-2"></i> View All Contacts
            </a>
        </div>
    </div>

    @if($contacts->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Call</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($contacts as $contact)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $contact->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'pending' => 'gray',
                                'calling' => 'blue',
                                'completed' => 'green',
                                'failed' => 'red',
                                'no-answer' => 'yellow',
                            ];
                            $color = $statusColors[$contact->status ?? 'pending'] ?? 'gray';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                            {{ ucfirst(str_replace('-', ' ', $contact->status ?? 'pending')) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->attempts ?? 0 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $contact->last_call_at ? $contact->last_call_at->format('Y-m-d H:i') : 'Never' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-address-book text-4xl mb-4 text-gray-400"></i>
        <p class="text-gray-500 mb-4">No contacts in this campaign yet</p>
        <a href="{{ route('campaigns.contacts.import', $campaign->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
            <i class="fas fa-upload mr-2"></i> Import Contacts
        </a>
    </div>
    @endif
</div>
@endsection

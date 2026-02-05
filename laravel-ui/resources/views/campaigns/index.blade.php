@extends('layouts.app')

@section('title', 'Campaigns')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Campaigns</h3>
        <p class="text-gray-600">Manage AI-powered calling campaigns</p>
    </div>
    <a href="{{ route('campaigns.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
        <i class="fas fa-plus mr-2"></i> Create Campaign
    </a>
</div>

<!-- Campaign Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Total Campaigns</p>
        <p class="text-3xl font-bold text-gray-800">{{ $campaigns->total() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Running</p>
        <p class="text-3xl font-bold text-green-600">{{ $runningCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Paused</p>
        <p class="text-3xl font-bold text-yellow-600">{{ $pausedCount ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Completed</p>
        <p class="text-3xl font-bold text-gray-600">{{ $completedCount ?? 0 }}</p>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('campaigns.index') }}" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search campaigns by name..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">All Statuses</option>
            <option value="running" {{ request('status') == 'running' ? 'selected' : '' }}>Running</option>
            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
            <option value="stopped" {{ request('status') == 'stopped' ? 'selected' : '' }}>Stopped</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i> Search
        </button>
        @if(request('search') || request('status'))
        <a href="{{ route('campaigns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
            <i class="fas fa-times mr-2"></i> Clear
        </a>
        @endif
    </form>
</div>

<!-- Campaigns Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Provider</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacts</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($campaigns as $campaign)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center">
                            <i class="fas fa-bullhorn text-pink-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($campaign->description ?? '', 50) }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $campaign->ai_provider ?? 'N/A' }}
                    @if($campaign->ai_model)
                    <div class="text-xs text-gray-500">{{ $campaign->ai_model }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $statusColors = [
                            'running' => 'green',
                            'paused' => 'yellow',
                            'stopped' => 'red',
                            'completed' => 'gray',
                        ];
                        $color = $statusColors[$campaign->status ?? 'stopped'] ?? 'gray';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                        {{ ucfirst($campaign->status ?? 'stopped') }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $campaign->total_contacts ?? 0 }} total
                    <div class="text-xs text-gray-400">{{ $campaign->completed_contacts ?? 0 }} completed</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $total = $campaign->total_contacts ?? 0;
                        $completed = $campaign->completed_contacts ?? 0;
                        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $progress }}%</p>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $campaign->created_at ? $campaign->created_at->format('Y-m-d') : 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        @if($campaign->status == 'running')
                        <form action="{{ route('campaigns.pause', $campaign->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Pause">
                                <i class="fas fa-pause"></i>
                            </button>
                        </form>
                        @elseif($campaign->status == 'paused' || $campaign->status == 'stopped')
                        <form action="{{ route('campaigns.start', $campaign->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900" title="Start">
                                <i class="fas fa-play"></i>
                            </button>
                        </form>
                        @endif
                        
                        @if($campaign->status == 'running' || $campaign->status == 'paused')
                        <form action="{{ route('campaigns.stop', $campaign->id) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to stop this campaign?');">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Stop">
                                <i class="fas fa-stop"></i>
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('campaigns.show', $campaign->id) }}" class="text-blue-600 hover:text-blue-900" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('campaigns.edit', $campaign->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-bullhorn text-4xl mb-4 text-gray-400"></i>
                    <p class="text-lg">No campaigns found</p>
                    <a href="{{ route('campaigns.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Create your first campaign
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($campaigns->hasPages())
<div class="mt-6">
    {{ $campaigns->links() }}
</div>
@endif
@endsection

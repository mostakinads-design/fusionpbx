@extends('layouts.app')

@section('title', 'Campaign Contacts')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('campaigns.show', $campaign->id) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Campaign
    </a>
    <a href="{{ route('campaigns.contacts.import', $campaign->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
        <i class="fas fa-upload mr-2"></i> Import Contacts
    </a>
</div>

<!-- Campaign Info -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-2xl font-bold text-gray-800">{{ $campaign->name }} - Contacts</h3>
    <p class="text-gray-600 mt-2">Total: {{ $contacts->total() }} contacts</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('campaigns.contacts.index', $campaign->id) }}" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by name or phone..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="calling" {{ request('status') == 'calling' ? 'selected' : '' }}>Calling</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="no-answer" {{ request('status') == 'no-answer' ? 'selected' : '' }}>No Answer</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i> Search
        </button>
        @if(request('search') || request('status'))
        <a href="{{ route('campaigns.contacts.index', $campaign->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
            <i class="fas fa-times mr-2"></i> Clear
        </a>
        @endif
    </form>
</div>

<!-- Contacts Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempts</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Call</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($contacts as $contact)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contact->phone }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->email ?? 'N/A' }}</td>
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ Str::limit($contact->result ?? 'N/A', 30) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="text-blue-600 hover:text-blue-900 mr-3" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <form action="{{ route('campaigns.contacts.destroy', [$campaign->id, $contact->id]) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this contact?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-address-book text-4xl mb-4 text-gray-400"></i>
                    <p class="text-lg">No contacts found</p>
                    <a href="{{ route('campaigns.contacts.import', $campaign->id) }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Import contacts
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($contacts->hasPages())
<div class="mt-6">
    {{ $contacts->links() }}
</div>
@endif
@endsection

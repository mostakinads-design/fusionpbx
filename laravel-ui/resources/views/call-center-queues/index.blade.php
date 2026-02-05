@extends('layouts.app')

@section('title', 'Call Center Queues')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Call Center Queues</h3>
        <p class="text-gray-600">Manage call center queues</p>
    </div>
    <a href="{{ route('call-center-queues.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
        <i class="fas fa-plus mr-2"></i> Add Queue
    </a>
</div>

<!-- Search -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('call-center-queues.index') }}" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search queues by name or extension..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i> Search
        </button>
        @if(request('search'))
        <a href="{{ route('call-center-queues.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
            <i class="fas fa-times mr-2"></i> Clear
        </a>
        @endif
    </form>
</div>

<!-- Queues Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extension</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Strategy</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agents</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($queues as $queue)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-list text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $queue->name }}</div>
                            <div class="text-sm text-gray-500">{{ $queue->description ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $queue->extension ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $queue->strategy ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $queue->agents_count ?? 0 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($queue->enabled ?? true)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                    </span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Inactive
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $queue->created_at ? $queue->created_at->format('Y-m-d') : 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('call-center-queues.show', $queue->id) }}" class="text-green-600 hover:text-green-900 mr-3">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('call-center-queues.edit', $queue->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('call-center-queues.destroy', $queue->id) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to delete this queue?');">
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
                    <i class="fas fa-list text-4xl mb-4 text-gray-400"></i>
                    <p class="text-lg">No queues found</p>
                    <a href="{{ route('call-center-queues.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Create your first queue
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($queues->hasPages())
<div class="mt-6">
    {{ $queues->links() }}
</div>
@endif
@endsection

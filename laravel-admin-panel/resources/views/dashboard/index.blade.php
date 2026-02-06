@extends('layouts.app')

@section('title', 'Dashboard - FusionPBX Admin Panel')

@section('content')
<div x-data="dashboard()" x-init="init()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Real-time call center statistics and insights</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Calls -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-4xl">📞</div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Calls Today</dt>
                            <dd class="text-2xl font-bold text-gray-900" x-text="stats.total_calls">{{ $todayStats['total_calls'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Answered Calls -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-4xl">✅</div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Answered Calls</dt>
                            <dd class="text-2xl font-bold text-green-600" x-text="stats.answered_calls">{{ $todayStats['answered_calls'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Missed Calls -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-4xl">❌</div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Missed Calls</dt>
                            <dd class="text-2xl font-bold text-red-600" x-text="stats.missed_calls">{{ $todayStats['missed_calls'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Duration -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-4xl">⏱️</div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Duration</dt>
                            <dd class="text-2xl font-bold text-gray-900" x-text="formatDuration(stats.avg_duration)">
                                {{ gmdate('i:s', $todayStats['avg_duration'] ?? 0) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Calls & Active Extensions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Calls -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Recent Calls
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @forelse($recentCalls as $call)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($call->hangup_cause === 'NORMAL_CLEARING')
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                            <span class="text-lg font-medium leading-none text-green-600">✓</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                                            <span class="text-lg font-medium leading-none text-red-600">✗</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $call->caller_id_name }} ({{ $call->caller_id_number }})
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        → {{ $call->destination_number }} | {{ gmdate('H:i:s', $call->duration) }}
                                    </p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $call->direction }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-4 text-center text-gray-500">
                            No recent calls found
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="mt-6">
                    <a href="{{ route('cdr.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        View all calls
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    System Status
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Active Extensions</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $activeExtensions }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inbound Calls</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900" x-text="stats.inbound_calls">{{ $todayStats['inbound_calls'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Outbound Calls</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900" x-text="stats.outbound_calls">{{ $todayStats['outbound_calls'] }}</dd>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('ai.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            🤖 Open AI Agent
                            <svg class="ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        stats: {
            total_calls: {{ $todayStats['total_calls'] }},
            answered_calls: {{ $todayStats['answered_calls'] }},
            missed_calls: {{ $todayStats['missed_calls'] }},
            avg_duration: {{ $todayStats['avg_duration'] ?? 0 }},
            inbound_calls: {{ $todayStats['inbound_calls'] }},
            outbound_calls: {{ $todayStats['outbound_calls'] }}
        },
        init() {
            // Refresh data every 30 seconds
            setInterval(() => this.refreshData(), 30000);
        },
        async refreshData() {
            try {
                const response = await fetch('/dashboard/live-data');
                const data = await response.json();
                this.stats = data.today;
            } catch (error) {
                console.error('Error refreshing data:', error);
            }
        },
        formatDuration(seconds) {
            if (!seconds) return '00:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
    }
}
</script>
@endpush

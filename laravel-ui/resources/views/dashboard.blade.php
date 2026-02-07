@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Total Users</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-users text-blue-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('users.index') }}" class="mt-4 text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
            View all <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Total Extensions Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Extensions</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalExtensions ?? 0 }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-phone text-green-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('extensions.index') }}" class="mt-4 text-green-600 hover:text-green-800 text-sm font-medium inline-flex items-center">
            View all <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Total Domains Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Domains</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalDomains ?? 0 }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-globe text-purple-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('domains.index') }}" class="mt-4 text-purple-600 hover:text-purple-800 text-sm font-medium inline-flex items-center">
            View all <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Daily Calls Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Daily Calls</p>
                <p class="text-3xl font-bold text-gray-800">{{ $dailyCalls ?? 0 }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-phone-volume text-yellow-600 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('cdr.index') }}" class="mt-4 text-yellow-600 hover:text-yellow-800 text-sm font-medium inline-flex items-center">
            View CDR <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

<!-- Call Center Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Queues Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Call Center Queues</h3>
            <i class="fas fa-list text-gray-400 text-xl"></i>
        </div>
        <p class="text-4xl font-bold text-gray-800 mb-2">{{ $totalQueues ?? 0 }}</p>
        <a href="{{ route('call-center-queues.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
            Manage queues <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Agents Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Active Agents</h3>
            <i class="fas fa-headset text-gray-400 text-xl"></i>
        </div>
        <p class="text-4xl font-bold text-gray-800 mb-2">{{ $totalAgents ?? 0 }}</p>
        <a href="{{ route('call-center-agents.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
            Manage agents <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Running Campaigns Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Running Campaigns</h3>
            <i class="fas fa-bullhorn text-gray-400 text-xl"></i>
        </div>
        <p class="text-4xl font-bold text-gray-800 mb-2">{{ $runningCampaigns ?? 0 }}</p>
        <a href="{{ route('campaigns.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center">
            View campaigns <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
    <div class="space-y-4">
        @forelse($recentActivity ?? [] as $activity)
        <div class="flex items-center border-b border-gray-200 pb-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-{{ $activity['icon'] ?? 'info-circle' }} text-blue-600"></i>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-800">{{ $activity['message'] ?? '' }}</p>
                <p class="text-xs text-gray-500">{{ $activity['time'] ?? '' }}</p>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">No recent activity</p>
        @endforelse
    </div>
</div>
@endsection

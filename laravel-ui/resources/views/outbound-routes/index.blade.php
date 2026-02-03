@extends('layout')
@section('title', 'Outbound Routes')
@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">📞 Outbound Routes</h1>
        <p class="mt-2 text-gray-600">Manage call routing with AI-powered optimization</p>
    </div>
    <a href="{{ route('outbound-routes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Create Route
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pattern</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gateway</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($routes as $route)
            <tr>
                <td class="px-6 py-4 font-mono">{{ $route->route_order }}</td>
                <td class="px-6 py-4">
                    <div class="font-medium">{{ $route->route_name }}</div>
                    @if($route->is_emergency)
                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">🚨 Emergency</span>
                    @endif
                </td>
                <td class="px-6 py-4 font-mono text-sm">{{ $route->destination_pattern }}</td>
                <td class="px-6 py-4">{{ $route->gateway_name }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ strtoupper($route->route_type) }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($route->ai_routing_enabled)
                        <div class="text-xs space-y-1">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded">🤖 Enabled</span>
                            @if($route->ai_cost_optimization)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded block mt-1">💰 Cost</span>
                            @endif
                            @if($route->ai_quality_optimization)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded block mt-1">⭐ Quality</span>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded {{ $route->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $route->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('outbound-routes.show', $route->route_uuid) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No outbound routes found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $routes->links() }}</div>
@endsection

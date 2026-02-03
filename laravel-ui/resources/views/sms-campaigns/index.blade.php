@extends('layout')
@section('title', 'SMS Campaigns')
@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">💬 SMS Campaigns</h1>
        <p class="mt-2 text-gray-600">Manage bulk SMS campaigns with AI-powered responses</p>
    </div>
    <a href="{{ route('sms-campaigns.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Create Campaign
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacts</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent/Delivered</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI Features</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($campaigns as $campaign)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium">{{ $campaign->campaign_name }}</div>
                    <div class="text-xs text-gray-500">{{ $campaign->sender_id }}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded {{ $campaign->campaign_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($campaign->campaign_status) }}
                    </span>
                </td>
                <td class="px-6 py-4">{{ $campaign->total_contacts }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm">{{ $campaign->sent_count }} / {{ $campaign->delivered_count }}</div>
                    <div class="text-xs text-gray-500">{{ $campaign->success_rate }}% success</div>
                </td>
                <td class="px-6 py-4">
                    @if($campaign->ai_enabled)
                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">🤖 AI Enabled</span>
                    @else
                        <span class="text-gray-400 text-xs">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('sms-campaigns.show', $campaign->sms_campaign_uuid) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                    @if($campaign->campaign_status === 'draft')
                        <form action="{{ route('sms-campaigns.start', $campaign) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900">Start</button>
                        </form>
                    @elseif($campaign->campaign_status === 'active')
                        <form action="{{ route('sms-campaigns.pause', $campaign) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900">Pause</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No SMS campaigns found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection

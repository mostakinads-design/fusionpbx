@extends('layout')
@section('title', 'Voice Broadcasts')
@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold">📢 Voice Broadcasts</h1>
        <p class="mt-2 text-gray-600">Automated voice messaging with AI conversation capabilities</p>
    </div>
    <a href="{{ route('voice-broadcasts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Create Broadcast
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacts/Called</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Answered</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI Features</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($broadcasts as $broadcast)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium">{{ $broadcast->broadcast_name }}</div>
                    <div class="text-xs text-gray-500">
                        @if($broadcast->text_to_speech)
                            🎙️ TTS
                        @else
                            🎵 Audio File
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded {{ $broadcast->broadcast_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($broadcast->broadcast_status) }}
                    </span>
                </td>
                <td class="px-6 py-4">{{ $broadcast->called_count }} / {{ $broadcast->total_contacts }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm">{{ $broadcast->answered_count }}</div>
                    <div class="text-xs text-gray-500">{{ round($broadcast->completion_rate, 1) }}%</div>
                </td>
                <td class="px-6 py-4">
                    @if($broadcast->ai_enabled)
                        <div class="text-xs">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded">🤖 AI</span>
                            @if($broadcast->ai_conversation_mode)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded mt-1">💬 Interactive</span>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('voice-broadcasts.show', $broadcast->broadcast_uuid) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No voice broadcasts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $broadcasts->links() }}</div>
@endsection

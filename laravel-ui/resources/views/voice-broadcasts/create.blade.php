@extends('layout')
@section('title', 'Create Voice Broadcast')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">📢 Create Voice Broadcast</h1>
    <p class="mt-2 text-gray-600">Set up automated voice messaging with AI conversation</p>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-4xl">
    <form method="POST" action="{{ route('voice-broadcasts.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Domain *</label>
                <select name="domain_uuid" required class="w-full rounded-md border-gray-300">
                    <option value="">Select Domain</option>
                    @foreach($domains as $domain)
                        <option value="{{ $domain->domain_uuid }}">{{ $domain->domain_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Broadcast Name *</label>
                <input type="text" name="broadcast_name" required class="w-full rounded-md border-gray-300">
            </div>
        </div>

        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold mb-4">Audio Content</h3>
            
            <div class="mb-4">
                <label class="flex items-center mb-2">
                    <input type="checkbox" name="text_to_speech" id="tts_enabled" class="rounded mr-2">
                    <span class="font-medium">Use Text-to-Speech (AI Generated)</span>
                </label>
            </div>

            <div id="tts_options" class="hidden mb-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Message Text</label>
                    <textarea name="tts_text" rows="4" class="w-full rounded-md border-gray-300" placeholder="Enter text to be spoken..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Voice</label>
                        <select name="tts_voice" class="w-full rounded-md border-gray-300">
                            <option value="alloy">Alloy (Neutral)</option>
                            <option value="echo">Echo (Male)</option>
                            <option value="fable">Fable (British)</option>
                            <option value="onyx">Onyx (Deep)</option>
                            <option value="nova">Nova (Female)</option>
                            <option value="shimmer">Shimmer (Warm)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="audio_upload" class="">
                <label class="block text-sm font-medium mb-2">Or Upload Audio File</label>
                <input type="file" name="audio_file" accept=".mp3,.wav" class="w-full">
                <p class="text-xs text-gray-500 mt-1">Max 10MB, MP3 or WAV format</p>
            </div>
        </div>

        <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
            <h3 class="font-semibold text-lg mb-4">🤖 AI Conversation Features</h3>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="ai_enabled" id="ai_enabled" class="rounded mr-2">
                    <span class="font-medium">Enable AI Agent</span>
                </label>
            </div>

            <div id="ai_options" class="hidden space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="ai_conversation_mode" class="rounded mr-2">
                        <span class="font-medium">Interactive Conversation Mode</span>
                    </label>
                    <p class="text-xs text-gray-600 ml-6">AI will listen and respond to recipient's speech</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">AI System Prompt</label>
                    <textarea name="ai_system_prompt" rows="3" class="w-full rounded-md border-gray-300" placeholder="Instructions for AI: Be professional and helpful..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Max Conversation Turns</label>
                    <input type="number" name="ai_max_conversation_turns" min="1" max="20" value="5" class="w-full rounded-md border-gray-300">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Max Retries</label>
                <input type="number" name="max_retries" value="3" min="1" max="10" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Caller ID Name</label>
                <input type="text" name="caller_id_name" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Caller ID Number</label>
                <input type="text" name="caller_id_number" class="w-full rounded-md border-gray-300">
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('voice-broadcasts.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded">Create Broadcast</button>
        </div>
    </form>
</div>

<script>
document.getElementById('tts_enabled').addEventListener('change', function() {
    document.getElementById('tts_options').classList.toggle('hidden', !this.checked);
    document.getElementById('audio_upload').classList.toggle('hidden', this.checked);
});
document.getElementById('ai_enabled').addEventListener('change', function() {
    document.getElementById('ai_options').classList.toggle('hidden', !this.checked);
});
</script>
@endsection

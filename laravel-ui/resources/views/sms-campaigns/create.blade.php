@extends('layout')
@section('title', 'Create SMS Campaign')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">💬 Create SMS Campaign</h1>
    <p class="mt-2 text-gray-600">Set up a new bulk SMS campaign with AI capabilities</p>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-4xl">
    <form method="POST" action="{{ route('sms-campaigns.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                <label class="block text-sm font-medium mb-2">Campaign Name *</label>
                <input type="text" name="campaign_name" required class="w-full rounded-md border-gray-300">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">SMS Template *</label>
            <textarea name="sms_template" rows="4" required class="w-full rounded-md border-gray-300" placeholder="Your SMS message here. Use {first_name}, {last_name} for personalization."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Sender ID *</label>
                <input type="text" name="sender_id" required maxlength="11" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Sending Rate (per minute) *</label>
                <input type="number" name="sending_rate" required min="1" max="100" value="10" class="w-full rounded-md border-gray-300">
            </div>
        </div>

        <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
            <h3 class="font-semibold text-lg mb-4">🤖 AI Features</h3>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="ai_enabled" id="ai_enabled" class="rounded mr-2">
                    <span class="font-medium">Enable AI-Powered Features</span>
                </label>
            </div>

            <div id="ai_options" class="hidden">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">AI Model</label>
                        <select name="ai_model" class="w-full rounded-md border-gray-300">
                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Fast)</option>
                            <option value="gpt-4">GPT-4 (Advanced)</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center mt-7">
                            <input type="checkbox" name="ai_reply_handling" class="rounded mr-2">
                            <span>Auto-Reply to Responses</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">AI Personality</label>
                    <textarea name="ai_personality" rows="2" class="w-full rounded-md border-gray-300" placeholder="Describe how AI should respond: friendly, professional, casual..."></textarea>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Schedule Type *</label>
            <select name="schedule_type" required class="w-full rounded-md border-gray-300">
                <option value="immediate">Send Immediately</option>
                <option value="scheduled">Schedule for Later</option>
                <option value="recurring">Recurring Campaign</option>
            </select>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('sms-campaigns.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded">Create Campaign</button>
        </div>
    </form>
</div>

<script>
document.getElementById('ai_enabled').addEventListener('change', function() {
    document.getElementById('ai_options').classList.toggle('hidden', !this.checked);
});
</script>
@endsection

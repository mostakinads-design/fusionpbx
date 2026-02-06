@extends('layouts.app')

@section('title', 'AI Agent - FusionPBX Admin Panel')

@section('content')
<div x-data="aiAgent()" x-init="init()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">🤖 AI Agent</h1>
        <p class="mt-1 text-sm text-gray-600">Interact with AI-powered call center assistant</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- AI Chat -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg flex flex-col" style="height: 600px;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">AI Chat Assistant</h3>
                <p class="text-sm text-gray-500">Ask questions about calls, statistics, or get help with routing decisions</p>
            </div>
            
            <!-- Chat Messages -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" id="chat-container">
                <!-- Welcome Message -->
                <div class="flex justify-start">
                    <div class="bg-gray-100 rounded-lg px-4 py-2 max-w-md">
                        <p class="text-sm text-gray-900">
                            👋 Hello! I'm your AI assistant. I can help you with:
                        </p>
                        <ul class="mt-2 text-sm text-gray-700 list-disc list-inside">
                            <li>Analyzing call data and trends</li>
                            <li>Making routing decisions</li>
                            <li>Answering questions about your call center</li>
                            <li>Providing insights and recommendations</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Chat messages will appear here -->
                <template x-for="(message, index) in messages" :key="index">
                    <div :class="message.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="message.role === 'user' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900'" 
                             class="rounded-lg px-4 py-2 max-w-md">
                            <p class="text-sm whitespace-pre-wrap" x-text="message.content"></p>
                        </div>
                    </div>
                </template>
                
                <!-- Loading indicator -->
                <div x-show="isLoading" class="flex justify-start">
                    <div class="bg-gray-100 rounded-lg px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <div class="animate-pulse">🤖</div>
                            <span class="text-sm text-gray-600">Thinking...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="px-6 py-4 border-t border-gray-200">
                <form @submit.prevent="sendMessage" class="flex space-x-2">
                    <input type="text" 
                           x-model="currentMessage" 
                           placeholder="Type your message..." 
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           :disabled="isLoading">
                    <button type="submit" 
                            :disabled="isLoading || !currentMessage.trim()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        📤 Send
                    </button>
                </form>
            </div>
        </div>

        <!-- AI Settings & Quick Actions -->
        <div class="space-y-6">
            <!-- AI Mode Settings -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">AI Mode</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="ai_mode" 
                                   value="human_only" 
                                   x-model="aiMode"
                                   @change="updateMode"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <span class="ml-3 block text-sm font-medium text-gray-700">
                                👤 Human Only
                            </span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="ai_mode" 
                                   value="hybrid" 
                                   x-model="aiMode"
                                   @change="updateMode"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <span class="ml-3 block text-sm font-medium text-gray-700">
                                🤝 Hybrid (AI + Human)
                            </span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="ai_mode" 
                                   value="ai_only" 
                                   x-model="aiMode"
                                   @change="updateMode"
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <span class="ml-3 block text-sm font-medium text-gray-700">
                                🤖 AI Only
                            </span>
                        </label>
                    </div>
                    
                    <p class="mt-4 text-xs text-gray-500">
                        Current mode: <strong x-text="aiMode"></strong>
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-2">
                    <button @click="quickQuestion('Analyze today\'s call statistics')" 
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                        📊 Today's Statistics
                    </button>
                    <button @click="quickQuestion('What are the most common hangup causes?')" 
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                        ❌ Hangup Analysis
                    </button>
                    <button @click="quickQuestion('Show me call patterns by hour')" 
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                        📈 Call Patterns
                    </button>
                    <button @click="quickQuestion('Give me recommendations to improve answer rate')" 
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                        💡 Get Recommendations
                    </button>
                </div>
            </div>

            <!-- API Status -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Status</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">OpenAI Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    🟢 Connected
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Model</dt>
                            <dd class="mt-1 text-sm text-gray-900">GPT-4</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function aiAgent() {
    return {
        messages: [],
        currentMessage: '',
        isLoading: false,
        aiMode: 'hybrid',
        
        init() {
            console.log('AI Agent initialized');
        },
        
        async sendMessage() {
            if (!this.currentMessage.trim() || this.isLoading) return;
            
            const userMessage = this.currentMessage.trim();
            this.messages.push({
                role: 'user',
                content: userMessage
            });
            this.currentMessage = '';
            this.isLoading = true;
            
            // Scroll to bottom
            this.$nextTick(() => {
                const container = document.getElementById('chat-container');
                container.scrollTop = container.scrollHeight;
            });
            
            try {
                const response = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: userMessage,
                        history: this.messages.slice(-10) // Send last 10 messages for context
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.messages.push({
                        role: 'assistant',
                        content: data.message
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: '❌ Sorry, I encountered an error. Please try again.'
                    });
                }
            } catch (error) {
                console.error('Chat error:', error);
                this.messages.push({
                    role: 'assistant',
                    content: '❌ Sorry, I could not connect to the AI service. Please check your configuration.'
                });
            } finally {
                this.isLoading = false;
                
                // Scroll to bottom
                this.$nextTick(() => {
                    const container = document.getElementById('chat-container');
                    container.scrollTop = container.scrollHeight;
                });
            }
        },
        
        quickQuestion(question) {
            this.currentMessage = question;
            this.sendMessage();
        },
        
        async updateMode() {
            try {
                const response = await fetch('/ai/update-mode', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        mode: this.aiMode
                    })
                });
                
                const data = await response.json();
                console.log('Mode updated:', data);
            } catch (error) {
                console.error('Mode update error:', error);
            }
        }
    }
}
</script>
@endpush

<div id="section-assistant" class="ff-card relative overflow-hidden"
     x-data="auditChat({{ $audit->id }}, {{ $project->id }})"
     x-init="init">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-500 via-indigo-500 to-purple-500"></div>
    <div class="p-6">
        <h3 class="ff-section-header flex items-center gap-2 mb-4">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-purple-600 text-white shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </span>
            {{ __('FTTH Assistant') }}
            <span class="ff-badge-info">{{ __('AI') }}</span>
        </h3>

        <div class="border border-surface-200 rounded-lg overflow-hidden">
            <div class="h-80 overflow-y-auto p-4 space-y-4 bg-surface-50" x-ref="messagesContainer">
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user'
                            ? 'bg-brand-600 text-white rounded-lg rounded-br-sm px-4 py-2 max-w-[80%]'
                            : 'bg-white border border-surface-200 rounded-lg rounded-bl-sm px-4 py-2 max-w-[80%]'">
                            <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
                        </div>
                    </div>
                </template>
                <div x-show="loading" class="flex justify-start">
                    <div class="bg-white border border-surface-200 rounded-lg rounded-bl-sm px-4 py-3">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
                <div x-show="!loading && messages.length === 0" class="text-center text-gray-400 text-sm py-8">
                    {{ __('Ask a question about this audit...') }}
                </div>
            </div>

            <form @submit.prevent="sendMessage" class="border-t border-surface-200 p-3 bg-white flex gap-2">
                <input type="text" x-model="message" name="message" id="assistant-message"
                    class="ff-input flex-1"
                    :placeholder="loading ? '{{ __('Thinking...') }}' : '{{ __('Ask a question...') }}'"
                    :disabled="loading" autocomplete="off">
                <button type="submit" :disabled="!message.trim() || loading"
                    class="ff-btn-primary px-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

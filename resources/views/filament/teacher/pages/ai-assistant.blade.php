<x-filament-panels::page>

<style>
/* Typing dots animation */
@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-6px); opacity: 1; }
}
.typing-dot {
    animation: typingBounce 1.2s ease-in-out infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.15s; }
.typing-dot:nth-child(3) { animation-delay: 0.30s; }

/* Progress steps animation */
@keyframes stepFadeIn {
    from { opacity: 0; transform: translateX(-8px); }
    to   { opacity: 1; transform: translateX(0); }
}
.step-item { animation: stepFadeIn 0.3s ease forwards; }

/* Shimmer effect */
@keyframes shimmer {
    0%   { background-position: -200% center; }
    100% { background-position:  200% center; }
}
.shimmer-text {
    background: linear-gradient(90deg, #94a3b8 25%, #e2e8f0 50%, #94a3b8 75%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer 1.5s linear infinite;
}

/* Pulse ring */
@keyframes pulseRing {
    0%   { transform: scale(1);   opacity: 0.8; }
    50%  { transform: scale(1.15); opacity: 0.4; }
    100% { transform: scale(1);   opacity: 0.8; }
}
.pulse-ring { animation: pulseRing 1.5s ease-in-out infinite; }

/* Fade in message */
@keyframes messageFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.message-new { animation: messageFadeIn 0.3s ease forwards; }
</style>

<div class="space-y-3">

    <div class="grid grid-cols-2 gap-2 lg:hidden">
        <button
            wire:click="$set('activeTab', 'history')"
            class="rounded-xl px-4 py-2 text-sm font-medium transition-colors
                {{ $this->activeTab === 'history'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-white text-gray-600 border border-gray-200' }}">
            Riwayat Chat
        </button>
        <button
            wire:click="$set('activeTab', 'chat')"
            class="rounded-xl px-4 py-2 text-sm font-medium transition-colors
                {{ $this->activeTab === 'chat'
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-white text-gray-600 border border-gray-200' }}">
            Chat AI
        </button>
    </div>

<div class="flex flex-col gap-4 lg:flex-row" style="min-height: calc(100vh - 140px)">

    {{-- ── Sidebar History ─────────────────────────────────────────────── --}}
    <aside class="{{ $this->activeTab === 'history' ? 'flex' : 'hidden' }} w-full flex-shrink-0 flex-col bg-white dark:bg-gray-800
                  border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden max-h-[45vh] lg:flex lg:w-64 lg:max-h-none">

        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Riwayat Chat</p>
            <button wire:click="startNewChat"
                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                title="Chat Baru">
                <x-heroicon-o-plus class="w-4 h-4 text-gray-500"/>
            </button>
        </div>

        <div class="p-3 border-b border-gray-200 dark:border-gray-700">
            <button wire:click="startNewChat"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-blue-600
                       hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                <x-heroicon-o-sparkles class="w-4 h-4"/>
                Chat Baru
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-2">
            @if(empty($this->sessions))
                <div class="px-4 py-8 text-center">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-8 h-8 text-gray-300 mx-auto mb-2"/>
                    <p class="text-xs text-gray-400">Belum ada riwayat chat</p>
                </div>
            @else
                @foreach($this->sessions as $session)
                    <div class="group relative mx-2 mb-1">
                        <button wire:click="loadSession('{{ $session['session_id'] }}')"
                            class="w-full text-left px-3 py-2.5 rounded-lg transition-colors text-xs
                                {{ $this->sessionId === $session['session_id']
                                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                                    : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <p class="font-medium line-clamp-2 pr-5">
                                {{ $session['session_title'] ?? 'Percakapan' }}
                            </p>
                            <p class="text-gray-400 mt-0.5 text-xs">
                                {{ \Carbon\Carbon::parse($session['last_message_at'])->diffForHumans() }}
                                · {{ $session['message_count'] }} pesan
                            </p>
                        </button>
                        <button wire:click="deleteSession('{{ $session['session_id'] }}')"
                            wire:confirm="Hapus percakapan ini?"
                            class="absolute right-2 top-2.5 p-1 rounded opacity-0 group-hover:opacity-100
                                   hover:bg-red-100 dark:hover:bg-red-900/30 transition-all"
                            title="Hapus">
                            <x-heroicon-o-trash class="w-3 h-3 text-red-400"/>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </aside>

    {{-- ── Area Chat Utama ──────────────────────────────────────────────── --}}
    <div class="{{ $this->activeTab === 'chat' ? 'flex' : 'hidden' }} flex-1 flex-col bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden min-h-[65vh] lg:flex lg:min-h-0">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-start gap-3 sm:px-5 sm:items-center">
            <div class="relative flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600
                            flex items-center justify-center">
                    <x-heroicon-s-sparkles class="w-4 h-4 text-white"/>
                </div>
                {{-- Online indicator --}}
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full
                            border-2 border-white dark:border-gray-800 pulse-ring"></div>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">AI Analytics Assistant</p>
                <p class="text-xs text-gray-400 transition-colors" wire:loading.remove wire:target="sendMessage,useSuggestion">
                    Powered by Google Gemini
                </p>
                <p class="text-xs text-blue-500 transition-colors" wire:loading wire:target="sendMessage,useSuggestion">
                    <span class="shimmer-text">Sedang menganalisis data...</span>
                </p>
            </div>

            <div class="w-full sm:ml-auto sm:w-auto sm:flex-1 sm:max-w-[120px]" wire:loading wire:target="sendMessage,useSuggestion">
                <div class="h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full
                                animate-pulse" style="width: 70%"></div>
                </div>
            </div>
        </div>

        {{-- Messages area --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-4 sm:p-4" id="chatMessages">

            {{-- Suggested questions --}}
            @if(count($this->messages) <= 1)
                <div class="grid grid-cols-1 gap-2 mb-2 sm:grid-cols-2">
                    @foreach($this->suggestions as $suggestion)
                        <button wire:click="useSuggestion('{{ addslashes($suggestion) }}')"
                            class="text-left p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200
                                   dark:border-gray-600 rounded-xl text-xs text-gray-600 dark:text-gray-400
                                   hover:border-blue-400 hover:text-blue-600 transition-all hover:shadow-sm">
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Messages --}}
            @foreach($this->messages as $index => $message)
                <div
                    wire:key="message-{{ $index }}-{{ $message['role'] }}"
                    class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} gap-2 message-new">

                    @if($message['role'] === 'assistant')
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-purple-600
                                    flex items-center justify-center flex-shrink-0 mt-1">
                            <x-heroicon-s-sparkles class="w-3.5 h-3.5 text-white"/>
                        </div>
                    @endif

                    <div class="max-w-[90%] sm:max-w-[78%]">
                        <div class="{{ $message['role'] === 'user'
                            ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm'
                            : 'bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl rounded-tl-sm' }}
                            px-4 py-2.5 shadow-sm">
                            @if($message['role'] === 'assistant')
                                <div class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed
                                            prose prose-sm dark:prose-invert max-w-none
                                            prose-p:my-1 prose-ul:my-1 prose-li:my-0">
                                    {!! \Illuminate\Support\Str::markdown($message['content']) !!}
                                </div>
                            @else
                                <p class="text-sm leading-relaxed">{{ $message['content'] }}</p>
                            @endif
                        </div>
                        @if($message['time'])
                            <p class="text-xs text-gray-400 mt-0.5 px-1
                                {{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                                {{ $message['time'] }}
                                @if(isset($message['saved']) && $message['saved'])
                                    · <span class="text-blue-400">tersimpan</span>
                                @endif
                            </p>
                        @endif
                    </div>

                    @if($message['role'] === 'user')
                        <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-600
                                    flex items-center justify-center flex-shrink-0 mt-1">
                            <x-heroicon-s-user class="w-3.5 h-3.5 text-gray-500"/>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- ── Loading Indicator ─────────────────────────────────── --}}
            <div
                wire:loading.flex
                wire:target="sendMessage,useSuggestion"
                class="flex justify-start gap-2 message-new"
                id="loadingIndicator">

                {{-- Avatar AI loading --}}
                <div class="flex-shrink-0 mt-1">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-purple-600
                                flex items-center justify-center">
                        <x-heroicon-s-sparkles class="w-3.5 h-3.5 text-white animate-spin"
                            style="animation-duration: 2s"/>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600
                            rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm max-w-xs">

                    {{-- Typing dots --}}
                    <div class="flex gap-1.5 items-center mb-2">
                        <div class="w-2 h-2 bg-blue-400 rounded-full typing-dot"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full typing-dot"></div>
                        <div class="w-2 h-2 bg-purple-500 rounded-full typing-dot"></div>
                    </div>

                    {{-- Progress steps --}}
                    <div class="space-y-1.5" id="loadingSteps">
                        <div class="flex items-center gap-2 step-item" style="animation-delay: 0s">
                            <div class="w-4 h-4 rounded-full bg-green-100 dark:bg-green-900/30
                                        flex items-center justify-center flex-shrink-0">
                                <x-heroicon-s-check class="w-2.5 h-2.5 text-green-500"/>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Menerima pertanyaan
                            </span>
                        </div>
                        <div class="flex items-center gap-2 step-item" style="animation-delay: 0.4s">
                            <div class="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-900/30
                                        flex items-center justify-center flex-shrink-0 animate-spin"
                                 style="animation-duration: 1.5s">
                                <x-heroicon-s-arrow-path class="w-2.5 h-2.5 text-blue-500"/>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 shimmer-text">
                                Mengambil data dari database...
                            </span>
                        </div>
                        <div class="flex items-center gap-2 step-item" style="animation-delay: 0.8s">
                            <div class="w-4 h-4 rounded-full bg-purple-100 dark:bg-purple-900/30
                                        flex items-center justify-center flex-shrink-0 animate-pulse">
                                <x-heroicon-s-sparkles class="w-2.5 h-2.5 text-purple-500"/>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Gemini AI sedang berpikir...
                            </span>
                        </div>
                        <div class="flex items-center gap-2 step-item" style="animation-delay: 1.2s">
                            <div class="w-4 h-4 rounded-full bg-orange-100 dark:bg-orange-900/30
                                        flex items-center justify-center flex-shrink-0 animate-pulse">
                                <x-heroicon-s-pencil-square class="w-2.5 h-2.5 text-orange-500"/>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Menyusun jawaban...
                            </span>
                        </div>
                    </div>

                    {{-- Estimated time --}}
                    <div class="mt-2.5 pt-2 border-t border-gray-200 dark:border-gray-600">
                        <p class="text-xs text-gray-400 flex items-center gap-1">
                            <x-heroicon-o-clock class="w-3 h-3"/>
                            Biasanya selesai dalam 3-5 detik
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input area --}}
        <div class="px-3 pb-4 pt-3 border-t border-gray-200 dark:border-gray-700 sm:px-4">

            {{-- Quick chips --}}
            <div class="mb-3 grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">
                <button wire:click="useSuggestion('Ujian paling banyak dikerjakan?')"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage,useSuggestion"
                    class="px-3 py-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                           rounded-full border border-blue-200 dark:border-blue-800 hover:bg-blue-100 text-left sm:text-center
                           transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    📊 Ujian terpopuler
                </button>
                <button wire:click="useSuggestion('Siapa siswa dengan nilai tertinggi?')"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage,useSuggestion"
                    class="px-3 py-1 text-xs bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400
                           rounded-full border border-green-200 dark:border-green-800 hover:bg-green-100 text-left sm:text-center
                           transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    🏆 Nilai tertinggi
                </button>
                <button wire:click="useSuggestion('Siswa mana yang perlu perhatian lebih?')"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage,useSuggestion"
                    class="px-3 py-1 text-xs bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400
                           rounded-full border border-red-200 dark:border-red-800 hover:bg-red-100 text-left sm:text-center
                           transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    ⚠️ Perlu perhatian
                </button>
                <button wire:click="useSuggestion('Ringkasan performa keseluruhan')"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage,useSuggestion"
                    class="px-3 py-1 text-xs bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                           rounded-full border border-purple-200 dark:border-purple-800 hover:bg-purple-100 text-left sm:text-center
                           transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    📈 Ringkasan
                </button>
            </div>

            {{-- Textarea + send button --}}
            <div class="flex gap-2 items-end">
                <div
                    class="flex-1 bg-gray-50 dark:bg-gray-700 border
                           border-gray-300 dark:border-gray-600
                           rounded-2xl overflow-hidden focus-within:border-blue-500 focus-within:ring-1
                           focus-within:ring-blue-500 transition-all"
                    wire:loading.class="border-blue-300 dark:border-blue-700 ring-1 ring-blue-200 dark:ring-blue-800"
                    wire:target="sendMessage,useSuggestion">
                    <textarea
                        wire:model="question"
                        wire:keydown.enter.prevent="sendMessage"
                        placeholder="Tanyakan tentang data e-learning... (Enter untuk kirim)"
                        rows="2"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage,useSuggestion"
                        class="w-full px-4 py-3 text-sm bg-transparent text-gray-900 dark:text-white
                               placeholder-gray-400 focus:outline-none resize-none
                               disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"></textarea>
                </div>

                <button wire:click="sendMessage"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage,useSuggestion"
                    wire:loading.class="bg-blue-300 dark:bg-blue-800 cursor-not-allowed"
                    @disabled($this->isLoading || empty(trim($this->question)))
                    class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all flex-shrink-0
                           bg-blue-600 hover:bg-blue-700 active:scale-95
                           disabled:opacity-60 text-white">
                    <span wire:loading.remove wire:target="sendMessage,useSuggestion">
                        <x-heroicon-s-paper-airplane class="w-4 h-4"/>
                    </span>
                    <span wire:loading wire:target="sendMessage,useSuggestion">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                </button>
            </div>

            {{-- Status bar --}}
            <div class="mt-2 flex flex-col gap-1 text-center sm:flex-row sm:items-center sm:justify-between sm:text-left">
                <p class="text-xs text-gray-400" wire:loading.remove wire:target="sendMessage,useSuggestion">
                    Powered by Google Gemini · Data diambil realtime
                </p>
                <p class="text-xs text-gray-400" wire:loading wire:target="sendMessage,useSuggestion">
                    <span class="text-blue-500 font-medium animate-pulse">
                        ● Memproses pertanyaan Anda...
                    </span>
                </p>
                <p class="text-xs text-gray-400">
                    Enter untuk kirim
                </p>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    function scrollToBottom() {
        const el = document.getElementById('chatMessages');
        if (el) el.scrollTop = el.scrollHeight;
    }
    document.addEventListener('livewire:updated', scrollToBottom);
    document.addEventListener('DOMContentLoaded', scrollToBottom);
</script>

</x-filament-panels::page>

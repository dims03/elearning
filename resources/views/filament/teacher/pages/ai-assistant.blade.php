<x-filament-panels::page>

<div class="flex flex-col" style="height: calc(100vh - 140px)">

    {{-- Chat messages area --}}
    <div class="flex-1 overflow-y-auto space-y-4 pb-4 pr-1" id="chatMessages">
        @if(count($this->messages) <= 1)
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach($this->suggestions as $suggestion)
                    <button
                        wire:click="useSuggestion('{{ addslashes($suggestion) }}')"
                        class="text-left p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                               rounded-xl text-xs text-gray-600 dark:text-gray-400 hover:border-blue-400
                               hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:shadow-sm">
                        {{ $suggestion }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Messages --}}
        @foreach($this->messages as $message)
            <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} gap-3">

                {{-- Avatar AI --}}
                @if($message['role'] === 'assistant')
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600
                                flex items-center justify-center flex-shrink-0 mt-1">
                        <x-heroicon-s-sparkles class="w-4 h-4 text-white"/>
                    </div>
                @endif

                {{-- Bubble --}}
                <div class="max-w-[80%]">
                    <div class="{{ $message['role'] === 'user'
                        ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm'
                        : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl rounded-tl-sm' }}
                        px-4 py-3 shadow-sm">

                        @if($message['role'] === 'assistant')
                            {{-- Render markdown-like formatting --}}
                            <div class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed prose-sm dark:prose-invert max-w-none">
                                {!! \Illuminate\Support\Str::markdown($message['content']) !!}
                            </div>
                        @else
                            <p class="text-sm leading-relaxed">{{ $message['content'] }}</p>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1 {{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                        {{ $message['time'] }}
                    </p>
                </div>

                {{-- Avatar User --}}
                @if($message['role'] === 'user')
                    <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700
                                flex items-center justify-center flex-shrink-0 mt-1">
                        <x-heroicon-s-user class="w-4 h-4 text-gray-500"/>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Loading indicator --}}
        @if($this->isLoading)
            <div class="flex justify-start gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600
                            flex items-center justify-center flex-shrink-0">
                    <x-heroicon-s-sparkles class="w-4 h-4 text-white animate-pulse"/>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-2xl rounded-tl-sm px-4 py-3">
                    <div class="flex gap-1.5 items-center">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        <span class="text-xs text-gray-400 ml-1">AI sedang menganalisis data...</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Input area --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">

        {{-- Suggestion chips (selalu tampil) --}}
        <div class="flex gap-2 mb-3 flex-wrap">
            <button wire:click="useSuggestion('Ujian paling banyak dikerjakan?')"
                class="px-3 py-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                       rounded-full border border-blue-200 dark:border-blue-800 hover:bg-blue-100 transition-colors">
                📊 Ujian terpopuler
            </button>
            <button wire:click="useSuggestion('Siapa siswa dengan nilai tertinggi?')"
                class="px-3 py-1 text-xs bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400
                       rounded-full border border-green-200 dark:border-green-800 hover:bg-green-100 transition-colors">
                🏆 Nilai tertinggi
            </button>
            <button wire:click="useSuggestion('Siswa mana yang perlu perhatian lebih?')"
                class="px-3 py-1 text-xs bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400
                       rounded-full border border-red-200 dark:border-red-800 hover:bg-red-100 transition-colors">
                ⚠️ Perlu perhatian
            </button>
            <button wire:click="useSuggestion('Berikan ringkasan performa keseluruhan')"
                class="px-3 py-1 text-xs bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                       rounded-full border border-purple-200 dark:border-purple-800 hover:bg-purple-100 transition-colors">
                📈 Ringkasan
            </button>
            <button wire:click="clearChat"
                class="px-3 py-1 text-xs bg-gray-50 dark:bg-gray-700 text-gray-500
                       rounded-full border border-gray-200 dark:border-gray-600 hover:bg-gray-100 transition-colors ml-auto">
                🗑️ Reset chat
            </button>
        </div>

        {{-- Input box --}}
        <div class="flex gap-3 items-end">
            <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600
                        rounded-2xl overflow-hidden focus-within:border-blue-500 focus-within:ring-1
                        focus-within:ring-blue-500 transition-all">
                <textarea
                    wire:model="question"
                    wire:keydown.enter.prevent="sendMessage"
                    placeholder="Tanyakan sesuatu tentang data e-learning Anda... (Enter untuk kirim)"
                    rows="2"
                    @disabled($this->isLoading)
                    class="w-full px-4 py-3 text-sm bg-transparent text-gray-900 dark:text-white
                           placeholder-gray-400 focus:outline-none resize-none disabled:opacity-50"></textarea>
            </div>

            <button
                wire:click="sendMessage"
                wire:loading.attr="disabled"
                @disabled($this->isLoading || empty(trim($this->question)))
                class="w-11 h-11 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-gray-700
                       text-white rounded-2xl flex items-center justify-center transition-colors flex-shrink-0">
                <span wire:loading.remove>
                    <x-heroicon-s-paper-airplane class="w-5 h-5"/>
                </span>
                <span wire:loading>
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
            </button>
        </div>

        <p class="text-xs text-gray-400 mt-2 text-center">
            Powered by Google Gemini · Data diambil secara realtime dari database
        </p>
    </div>
</div>

<script>
    document.addEventListener('livewire:updated', function () {
        const chat = document.getElementById('chatMessages');
        if (chat) chat.scrollTop = chat.scrollHeight;
    });
    const chat = document.getElementById('chatMessages');
    if (chat) chat.scrollTop = chat.scrollHeight;
</script>

</x-filament-panels::page>

<?php

namespace App\Filament\Teacher\Pages;

use App\Services\AnalyticsDataService;
use App\Services\GeminiChatService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AiAssistant extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Sparkles;
    protected static ?string $navigationLabel = 'AI Assistant';
    protected static ?string $title           = 'AI Analytics Assistant';
    protected string $view             = 'filament.teacher.pages.ai-assistant';
    protected static ?int $navigationSort     = 2;

    // Chat state
    public array $messages  = [];
    public string $question = '';
    public bool $isLoading  = false;

    // Suggested questions
    public array $suggestions = [
        'Ujian mana yang paling banyak dikerjakan siswa?',
        'Siapa siswa dengan nilai tertinggi?',
        'Kursus mana yang paling populer?',
        'Berapa tingkat kelulusan ujian saya?',
        'Siswa mana yang perlu mendapat perhatian lebih?',
        'Tampilkan ringkasan performa platform bulan ini',
        'Ujian mana yang memiliki nilai rata-rata terendah?',
        'Berikan rekomendasi untuk meningkatkan tingkat kelulusan',
    ];

    public function mount(): void
    {
        // Pesan sambutan
        $this->messages = [
            [
                'role'    => 'assistant',
                'content' => "Halo! 👋 Saya adalah **AI Analytics Assistant** untuk platform e-learning Anda.\n\nSaya dapat membantu Anda memahami:\n- 📊 Performa ujian dan nilai siswa\n- 🏆 Siapa siswa terbaik dan yang perlu perhatian\n- 📚 Statistik kursus dan enrollment\n- 📈 Tren dan insight dari data pembelajaran\n\nSilakan tanyakan apa saja tentang data platform Anda!",
                'time'    => now()->format('H:i'),
            ],
        ];
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->question))) return;

        $userQuestion = trim($this->question);
        $this->question = '';
        $this->messages[] = [
            'role'    => 'user',
            'content' => $userQuestion,
            'time'    => now()->format('H:i'),
        ];

        $this->isLoading = true;

        try {
            $contextData = app(AnalyticsDataService::class)->getContextData();
            $history = collect($this->messages)
                ->slice(-11, 10) // exclude pesan user terbaru
                ->filter(fn ($m) => $m['role'] !== 'system')
                ->map(fn ($m) => [
                    'role'    => $m['role'],
                    'content' => $m['content'],
                ])
                ->values()
                ->toArray();

            // Tanya ke Gemini
            $answer = app(GeminiChatService::class)->ask(
                question:    $userQuestion,
                contextData: $contextData,
                history:     $history,
            );

            $this->messages[] = [
                'role'    => 'assistant',
                'content' => $answer,
                'time'    => now()->format('H:i'),
            ];

        } catch (\Exception $e) {
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => '❌ Maaf, terjadi kesalahan: ' . $e->getMessage(),
                'time'    => now()->format('H:i'),
            ];
        }

        $this->isLoading = false;
    }

    public function useSuggestion(string $suggestion): void
    {
        $this->question = $suggestion;
        $this->sendMessage();
    }

    public function clearChat(): void
    {
        $this->mount();
    }
}

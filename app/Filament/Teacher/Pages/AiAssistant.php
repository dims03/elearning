<?php

namespace App\Filament\Teacher\Pages;

use App\Models\AiChatHistory;
use App\Services\AnalyticsDataService;
use App\Services\GeminiChatService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
    public ?string $sessionId = null;
    public array $sessions = [];
    public string $activeTab = 'chat';

    public array $suggestions = [
        'Ujian mana yang paling banyak dikerjakan siswa?',
        'Siapa siswa dengan nilai tertinggi?',
        'Kursus mana yang paling populer?',
        'Berapa tingkat kelulusan ujian saya?',
        'Siswa mana yang perlu mendapat perhatian lebih?',
        'Tampilkan ringkasan performa platform',
        'Ujian mana yang memiliki nilai rata-rata terendah?',
        'Berikan rekomendasi untuk meningkatkan kelulusan',
    ];


    public function mount(): void
    {
        $this->loadSessions();
        $this->startNewChat();
    }

    public function loadSessions(): void
    {
        $this->sessions = AiChatHistory::getSessionsForUser(Auth::id())
            ->map(fn ($s) => [
                'session_id'      => $s->session_id,
                'session_title'   => filled($s->session_title) ? $s->session_title : 'Percakapan',
                'last_message_at' => $s->last_message_at,
                'message_count'   => $s->message_count,
            ])
            ->toArray();
    }

    public function startNewChat(): void
    {
        $this->sessionId = (string) Str::uuid();
        $this->messages  = [
            [
                'role'    => 'assistant',
                'content' => "Halo! 👋 Saya adalah **AI Analytics Assistant** untuk platform e-learning Anda.\n\nSaya dapat membantu Anda memahami:\n- 📊 Performa ujian dan nilai siswa\n- 🏆 Siapa siswa terbaik dan yang perlu perhatian\n- 📚 Statistik kursus dan enrollment\n- 📈 Tren dan insight dari data pembelajaran\n\nSilakan tanyakan apa saja tentang data platform Anda!",
                'time'    => now()->format('H:i'),
                'saved'   => false,
            ],
        ];
        $this->activeTab = 'chat';
    }

    public function loadSession(string $sessionId): void
    {
        $this->sessionId = $sessionId;
        $this->activeTab = 'chat';

        $dbMessages = AiChatHistory::getMessages(Auth::id(), $sessionId);

        $this->messages = [
            [
                'role'    => 'assistant',
                'content' => "Halo! 👋 Ini adalah riwayat percakapan sebelumnya. Anda bisa melanjutkan percakapan dari sini.",
                'time'    => '',
                'saved'   => true,
            ],
        ];

        foreach ($dbMessages as $msg) {
            $this->messages[] = [
                'role'    => $msg->role,
                'content' => $msg->content,
                'time'    => $msg->created_at->format('H:i'),
                'saved'   => true,
            ];
        }
    }

    public function deleteSession(string $sessionId): void
    {
        AiChatHistory::where('user_id', Auth::id())
            ->where('session_id', $sessionId)
            ->delete();

        if ($this->sessionId === $sessionId) {
            $this->startNewChat();
        }

        $this->loadSessions();

        Notification::make()
            ->title('Percakapan dihapus.')
            ->success()
            ->send();
    }

    public function sendMessage(): void
    {
        if ($this->isLoading || empty(trim($this->question))) {
            return;
        }

        $userQuestion    = trim($this->question);
        $this->question  = '';
        $this->isLoading = true;
        $this->activeTab = 'chat';

        $this->messages[] = [
            'role'    => 'user',
            'content' => $userQuestion,
            'time'    => now()->format('H:i'),
            'saved'   => false,
        ];

        $isFirstMessage = AiChatHistory::where('user_id', Auth::id())
            ->where('session_id', $this->sessionId)
            ->doesntExist();

        $history = AiChatHistory::where('user_id', Auth::id())
            ->where('session_id', $this->sessionId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => [
                'role' => $m->role,
                'content' => $m->content,
            ])
            ->toArray();

        AiChatHistory::create([
            'user_id'       => Auth::id(),
            'session_id'    => $this->sessionId,
            'session_title' => $isFirstMessage
                ? Str::limit($userQuestion, 60)
                : null,
            'role'          => 'user',
            'content'       => $userQuestion,
        ]);

        try {
            // Context data dari DB
            $contextData = app(AnalyticsDataService::class)->getContextData();

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
                'saved'   => false,
            ];

            AiChatHistory::create([
                'user_id'    => Auth::id(),
                'session_id' => $this->sessionId,
                'role'       => 'assistant',
                'content'    => $answer,
            ]);

            $this->loadSessions();
        } catch (\Exception $e) {
            $this->messages[] = [
                'role'    => 'assistant',
                'content' => '❌ Maaf, terjadi kesalahan: ' . $e->getMessage(),
                'time'    => now()->format('H:i'),
                'saved'   => false,
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function useSuggestion(string $suggestion): void
    {
        $this->question = $suggestion;
        $this->sendMessage();
    }
}

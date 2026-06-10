<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    private string $apiKey;
    private string $model;
    private array $fallbackModels;
    private string $baseUrl;
    private string $apiVersion;
    private int $timeout;
    private int $retryAttempts;
    private int $retryDelayMs;

    public function __construct()
    {
        $this->apiKey = (string) config('gemini.api_key', env('GEMINI_API_KEY'));
        $this->model = (string) config('gemini.model', env('GEMINI_MODEL', 'gemini-3.5-flash'));
        $this->fallbackModels = array_values(array_filter(
            (array) config('gemini.fallback_models', []),
            static fn (mixed $model): bool => is_string($model) && trim($model) !== ''
        ));
        $this->baseUrl = rtrim((string) config('gemini.base_url', 'https://generativelanguage.googleapis.com'), '/');
        $this->apiVersion = (string) config('gemini.api_version', 'v1');
        $this->timeout = (int) config('gemini.request_timeout', 30);
        $this->retryAttempts = max(1, (int) config('gemini.retry_attempts', 3));
        $this->retryDelayMs = max(100, (int) config('gemini.retry_delay_ms', 1000));
    }

    public function ask(string $question, array $contextData, array $history = []): string
    {
        if ($this->apiKey === '') {
            Log::error('Gemini API key is missing.');
            return 'Maaf, konfigurasi AI belum lengkap. API key Gemini belum diatur.';
        }

        $systemPrompt = $this->buildSystemPrompt($contextData);
        $messages     = $this->buildMessages($systemPrompt, $question, $history);
        $payload = [
            'contents'         => $messages,
            'generationConfig' => [
                'temperature'     => 0.3,
                'maxOutputTokens' => 2048,
            ],
        ];

        try {
            $lastError = null;

            foreach ($this->getCandidateModels() as $model) {
                $response = $this->sendRequestWithRetry($model, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $candidate = $data['candidates'][0] ?? null;
                    $answer = $this->extractTextFromCandidate($candidate);

                    Log::info('Gemini response received.', [
                        'model' => $model,
                        'finish_reason' => $candidate['finishReason'] ?? null,
                        'parts_count' => count($candidate['content']['parts'] ?? []),
                    ]);

                    if ($answer !== '') {
                        return $answer;
                    }

                    return 'Maaf, tidak ada respons dari AI.';
                }

                $lastError = [
                    'status' => $response->status(),
                    'model' => $model,
                    'api_version' => $this->apiVersion,
                    'body' => $response->body(),
                ];

                Log::warning('Gemini model fallback triggered.', $lastError);
            }

            Log::error('Gemini API error', $lastError ?? [
                'model' => $this->model,
                'api_version' => $this->apiVersion,
                'body' => 'Unknown Gemini error.',
            ]);

            return $this->buildErrorMessage($lastError['status'] ?? null);

        } catch (\Exception $e) {
            Log::error('Gemini exception', ['message' => $e->getMessage()]);
            return 'Maaf, terjadi kesalahan: ' . $e->getMessage();
        }
    }

    private function getCandidateModels(): array
    {
        return array_values(array_unique([
            $this->model,
            ...$this->fallbackModels,
        ]));
    }

    private function sendRequestWithRetry(string $model, array $payload): Response
    {
        $response = null;

        for ($attempt = 1; $attempt <= $this->retryAttempts; $attempt++) {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'x-goog-api-key' => $this->apiKey,
                ])
                ->post("{$this->baseUrl}/{$this->apiVersion}/models/{$model}:generateContent", $payload);

            if ($response->successful()) {
                return $response;
            }

            if (! $this->shouldRetry($response->status()) || $attempt === $this->retryAttempts) {
                return $response;
            }

            $delayMs = $this->retryDelayMs * (2 ** ($attempt - 1));

            Log::warning('Gemini temporary failure, retrying request.', [
                'status' => $response->status(),
                'model' => $model,
                'attempt' => $attempt,
                'next_retry_in_ms' => $delayMs,
            ]);

            usleep($delayMs * 1000);
        }

        return $response;
    }

    private function shouldRetry(int $status): bool
    {
        return in_array($status, [429, 500, 502, 503, 504], true);
    }

    private function buildErrorMessage(?int $status): string
    {
        if (in_array($status, [429, 503], true)) {
            return 'Maaf, layanan AI sedang sibuk. Silakan coba lagi beberapa saat lagi.';
        }

        return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
    }

    private function extractTextFromCandidate(?array $candidate): string
    {
        if (! is_array($candidate)) {
            return '';
        }

        $parts = $candidate['content']['parts'] ?? [];

        if (! is_array($parts)) {
            return '';
        }

        $texts = collect($parts)
            ->map(fn ($part) => is_array($part) ? trim((string) ($part['text'] ?? '')) : '')
            ->filter()
            ->values()
            ->all();

        return trim(implode("\n", $texts));
    }

    private function buildSystemPrompt(array $data): string
    {
        $summary      = json_encode($data['summary'],          JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $topExams     = json_encode($data['top_exams'],        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $examStats    = json_encode($data['exam_stats'],       JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $courseStats  = json_encode($data['course_stats'],     JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $topStudents  = json_encode($data['top_students'],     JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $failStudents = json_encode($data['failing_students'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $recent       = json_encode($data['recent_sessions'],  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Kamu adalah asisten analitik cerdas untuk platform e-learning. Tugasmu adalah membantu guru dan admin memahami data pembelajaran dan performa siswa.

Jawab SELALU dalam Bahasa Indonesia yang ramah, jelas, dan informatif.
Gunakan emoji yang relevan untuk membuat jawaban lebih menarik.
Jika ada data konkret, tampilkan dalam format yang mudah dibaca.
Berikan insight dan rekomendasi berdasarkan data yang ada.
Jika pengguna meminta saran, rekomendasi, solusi, tindak lanjut, atau cara meningkatkan sesuatu, berikan jawaban yang konkret dan bisa dieksekusi.
Untuk pertanyaan rekomendasi, jangan berhenti di ringkasan angka. Wajib berikan:
1. masalah utamanya,
2. kemungkinan penyebab berdasarkan data,
3. minimal 3 langkah saran yang spesifik,
4. prioritas tindakan mana yang paling penting lebih dulu.
Jika datanya mendukung, sebutkan nama ujian, kursus, atau kelompok siswa yang perlu difokuskan.
Hindari jawaban yang menggantung atau berhenti di tengah kalimat.

=== DATA SAAT INI ===

📊 RINGKASAN UMUM:
{$summary}

🏆 UJIAN PALING BANYAK DIKERJAKAN:
{$topExams}

📝 STATISTIK DETAIL SETIAP UJIAN:
{$examStats}

📚 STATISTIK KURSUS:
{$courseStats}

⭐ SISWA NILAI TERTINGGI:
{$topStudents}

⚠️ SISWA YANG PERLU PERHATIAN (nilai terendah):
{$failStudents}

🕐 AKTIVITAS UJIAN TERBARU:
{$recent}

=== AKHIR DATA ===

Gunakan data di atas untuk menjawab pertanyaan pengguna. Jika ditanya sesuatu yang tidak ada dalam data, sampaikan dengan sopan bahwa data tersebut tidak tersedia.
PROMPT;
    }

    private function buildMessages(string $systemPrompt, string $question, array $history): array
    {
        $messages = [];

        $messages[] = [
            'role'  => 'user',
            'parts' => [['text' => $systemPrompt]],
        ];
        $messages[] = [
            'role'  => 'model',
            'parts' => [['text' => 'Baik! Saya siap membantu menganalisis data e-learning Anda. Apa yang ingin Anda ketahui?']],
        ];

        foreach ($history as $msg) {
            if (! isset($msg['role'], $msg['content']) || trim((string) $msg['content']) === '') {
                continue;
            }

            $messages[] = [
                'role'  => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $messages[] = [
            'role'  => 'user',
            'parts' => [['text' => $question]],
        ];

        return $messages;
    }
}

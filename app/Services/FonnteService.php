<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use RuntimeException;

class FonnteService
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {}

    public function sendMessage(string $target, string $message, array $options = []): array
    {
        return $this->post(config('services.fonnte.url'), [
            'target' => $target,
            'message' => $message,
            'countryCode' => $options['countryCode'] ?? config('services.fonnte.country_code'),
            'typing' => $this->toFonnteBool($options['typing'] ?? false),
            'duration' => $options['duration'] ?? null,
            'delay' => $options['delay'] ?? null,
            'schedule' => $options['schedule'] ?? null,
            'url' => $options['url'] ?? null,
            'filename' => $options['filename'] ?? null,
            'preview' => array_key_exists('preview', $options)
                ? $this->toFonnteBool((bool) $options['preview'])
                : null,
            'inboxid' => $options['inboxid'] ?? null,
            'followup' => $options['followup'] ?? null,
            'location' => $options['location'] ?? null,
        ]);
    }

    public function sendTypedMessage(
        string $target,
        string $message,
        ?int $duration = null,
        array $options = [],
    ): array {
        return $this->sendMessage($target, $message, array_merge($options, [
            'typing' => true,
            'duration' => $duration ?? config('services.fonnte.default_typing_duration'),
        ]));
    }

    public function startTyping(string $target, ?int $duration = null, bool $stop = false): array
    {
        return $this->post(config('services.fonnte.typing_url'), [
            'target' => $target,
            'countryCode' => config('services.fonnte.country_code'),
            'duration' => max(1, $duration ?? config('services.fonnte.default_typing_duration')),
            'stop' => $this->toFonnteBool($stop),
        ]);
    }

    public function stopTyping(string $target): array
    {
        return $this->startTyping($target, 1, true);
    }

    private function post(?string $url, array $payload): array
    {
        $token = (string) config('services.fonnte.token');

        if (blank($token)) {
            throw new RuntimeException('FONNTE_TOKEN belum diatur di environment.');
        }

        if (blank($url)) {
            throw new RuntimeException('Endpoint Fonnte belum diatur di config/services.php.');
        }

        $response = $this->http
            ->withHeaders([
                'Authorization' => $token,
            ])
            ->asForm()
            ->acceptJson()
            ->timeout(15)
            ->post($url, $this->filterPayload($payload))
            ->throw();

        return $response->json() ?? ['raw' => $response->body()];
    }

    private function filterPayload(array $payload): array
    {
        return array_filter($payload, static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    private function toFonnteBool(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}

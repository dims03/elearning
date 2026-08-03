<?php

namespace Tests\Feature;

use App\Services\FonnteService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteServiceTest extends TestCase
{
    public function test_send_typed_message_uses_send_endpoint_with_typing_payload(): void
    {
        Config::set('services.fonnte', [
            'token' => 'test-token',
            'url' => 'https://api.fonnte.com/send',
            'typing_url' => 'https://api.fonnte.com/typing',
            'country_code' => '62',
            'default_typing_duration' => 3,
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $service = app(FonnteService::class);

        $service->sendTypedMessage('08123456789', 'Halo dari Laravel', 4);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '08123456789'
                && $request['message'] === 'Halo dari Laravel'
                && $request['typing'] === 'true'
                && $request['duration'] === '4'
                && $request->hasHeader('Authorization', 'test-token');
        });
    }

    public function test_start_typing_uses_typing_endpoint(): void
    {
        Config::set('services.fonnte', [
            'token' => 'test-token',
            'url' => 'https://api.fonnte.com/send',
            'typing_url' => 'https://api.fonnte.com/typing',
            'country_code' => '62',
            'default_typing_duration' => 3,
        ]);

        Http::fake([
            'https://api.fonnte.com/typing' => Http::response(['status' => true], 200),
        ]);

        $service = app(FonnteService::class);

        $service->startTyping('08123456789', 5);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/typing'
                && $request['target'] === '08123456789'
                && $request['countryCode'] === '62'
                && $request['duration'] === '5'
                && $request['stop'] === 'false'
                && $request->hasHeader('Authorization', 'test-token');
        });
    }
}

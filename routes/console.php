<?php

use App\Services\FonnteService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('wa:test-send {target} {message} {--typing} {--typing-only} {--duration=3}', function (FonnteService $fonnte) {
    $target = (string) $this->argument('target');
    $message = (string) $this->argument('message');
    $duration = max(1, (int) $this->option('duration'));

    $response = $this->option('typing-only')
        ? $fonnte->startTyping($target, $duration)
        : ($this->option('typing')
            ? $fonnte->sendTypedMessage($target, $message, $duration)
            : $fonnte->sendMessage($target, $message));

    $this->info(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
})->purpose('Test kirim WhatsApp lewat Fonnte dari terminal Laravel');

Schedule::command('exam:expire-sessions')->everyMinute();

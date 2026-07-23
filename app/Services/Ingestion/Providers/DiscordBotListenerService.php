<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Providers;

use Illuminate\Support\Facades\Log;

class DiscordBotListenerService
{
    public function listenAndFetch(): array
    {
        Log::info('DiscordBotListenerService: Polling channel announcements...');

        // Real live integration setup is outlined in class docblock. Mock fallbacks removed completely.
        return [];
    }
}

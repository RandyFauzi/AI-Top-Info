<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Providers;

use Illuminate\Support\Facades\Log;

/**
 * Discord Ingestion Architecture:
 * 
 * To listen to live Discord payloads, run a background worker (via Laravel Reverb, Node.js bot instance, or php-discord/discord-php):
 * 1. Build a separate daemon bot script using `discord-php/discord-php`.
 * 2. Start the listener websocket loop:
 *    ```php
 *    $discord = new \Discord\Discord(['token' => env('DISCORD_BOT_TOKEN')]);
 *    $discord->on('ready', function ($discord) {
 *        $discord->on('message', function ($message, $discord) {
 *            if (is_relevant_channel($message->channel_id)) {
 *                // Dispatch ProcessRawDataWithGeminiJob on incoming chat message
 *                dispatch(new \App\Jobs\ProcessRawDataWithGeminiJob($message->content, 'Discord', $message->url));
 *            }
 *        });
 *    });
 *    $discord->run();
 *    ```
 */
class DiscordBotListenerService
{
    public function listenAndFetch(): array
    {
        Log::info('DiscordBotListenerService: Polling channel announcements...');

        // Return mock Discord opportunity chat signals
        return [
            [
                'content' => 'Dr_SoraAI: We are compiling spatial navigation drone datasets. Need 1080p POV video files showcasing vehicle-collision avoidance. Hit up hiring@visiondrive.ai or WA +14155550177.',
                'source_platform' => 'Discord',
                'source_url' => 'https://discord.com/channels/87654321/98765/12345678',
                'posted_at' => now()->subMinutes(30),
            ]
        ];
    }
}

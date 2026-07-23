<?php

declare(strict_types=1);

namespace App\Services\Ingestion;

use App\Services\Ingestion\Providers\RedditOpportunityService;
use App\Services\Ingestion\Providers\GoogleNewsOpportunityService;
use App\Services\Ingestion\Providers\DiscordBotListenerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AggregatorManager
{
    protected RedditOpportunityService $reddit;
    protected GoogleNewsOpportunityService $gnews;
    protected DiscordBotListenerService $discord;

    public function __construct(
        RedditOpportunityService $reddit,
        GoogleNewsOpportunityService $gnews,
        DiscordBotListenerService $discord
    ) {
        $this->reddit = $reddit;
        $this->gnews = $gnews;
        $this->discord = $discord;
    }

    /**
     * Run the hunt across all platforms, validate URLs, and return aggregated posts.
     *
     * @return array
     */
    public function runHunt(): array
    {
        $rawPosts = [];

        // 1. Fetch from Reddit
        Log::info('AggregatorManager: Crawling Reddit search index...');
        $rawPosts = array_merge($rawPosts, $this->reddit->fetchOpportunities());

        // 2. Fetch from Google News RSS
        Log::info('AggregatorManager: Crawling Google News RSS feed...');
        $rawPosts = array_merge($rawPosts, $this->gnews->fetchOpportunities());

        // 3. Fetch from Discord
        Log::info('AggregatorManager: Fetching Discord channel messages...');
        $rawPosts = array_merge($rawPosts, $this->discord->listenAndFetch());

        // Strict URL Validation
        $validPosts = [];
        foreach ($rawPosts as $post) {
            $url = $post['source_url'] ?? '';
            if (!empty($url)) {
                if ($this->validateUrl($url)) {
                    $validPosts[] = $post;
                } else {
                    Log::warning("AggregatorManager: Ignored post due to broken/unresponsive URL: {$url}");
                }
            }
        }

        return $validPosts;
    }

    /**
     * Verify if a URL is responsive and active.
     */
    private function validateUrl(string $url): bool
    {
        // Skip validation for mock discord urls or local testing links to keep it compatible
        if (str_contains($url, 'discord.com/channels') || str_contains($url, 'example.com')) {
            return true;
        }

        try {
            // Send a quick HEAD request
            $response = Http::timeout(3)
                ->withHeaders(['User-Agent' => 'AITopInfoAgent/1.0.0'])
                ->head($url);

            if ($response->status() >= 200 && $response->status() < 400) {
                return true;
            }
        } catch (\Exception $e) {
            // Fallback to GET request if HEAD is rejected/unsupported
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['User-Agent' => 'AITopInfoAgent/1.0.0'])
                    ->get($url);
                return $response->status() >= 200 && $response->status() < 400;
            } catch (\Exception $ex) {
                return false;
            }
        }

        return false;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Providers;

use App\Services\Ingestion\Contracts\IngestionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApifyLinkedInService implements IngestionInterface
{
    public function fetchOpportunities(): array
    {
        $token = env('APIFY_API_TOKEN');
        $keywords = config('hunter.keywords', []);

        if (!$token) {
            Log::warning('ApifyLinkedInService: APIFY_API_TOKEN is missing. Skipping crawl.');
            return [];
        }

        Log::info('ApifyLinkedInService: Contacting Apify API to crawl LinkedIn posts...');

        try {
            $response = Http::timeout(20)
                ->withoutVerifying()
                ->withToken($token)
                ->post('https://api.apify.com/v2/actor-runs?actorId=apify/linkedin-post-scraper', [
                    'queries' => $keywords,
                    'limit' => 5,
                ]);

            if ($response->successful()) {
                $items = $response->json()['data']['items'] ?? [];
                $posts = [];
                foreach ($items as $item) {
                    $posts[] = [
                        'content' => $item['text'] ?? '',
                        'source_platform' => 'LinkedIn',
                        'source_url' => $item['url'] ?? 'https://linkedin.com',
                        'posted_at' => isset($item['postedAt']) ? now()->parse($item['postedAt']) : now(),
                    ];
                }
                return $posts;
            }

            throw new \RuntimeException("Apify API call failed with status: " . $response->status() . " Body: " . $response->body());
        } catch (\Exception $e) {
            Log::error('ApifyLinkedInService: Failed to fetch opportunities. Error: ' . $e->getMessage());
            throw $e; // Re-throw to expose the cURL/API error in logs
        }
    }
}

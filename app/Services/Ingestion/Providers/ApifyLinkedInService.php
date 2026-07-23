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

        if ($token) {
            try {
                // Call Apify Actor to scrape LinkedIn posts based on boolean search keywords
                $response = Http::timeout(20)
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
            } catch (\Exception $e) {
                Log::error('ApifyLinkedInService: Scraper failed. Fallback to mock: ' . $e->getMessage());
            }
        }

        // Mock LinkedIn Opportunity Signals
        return [
            [
                'content' => 'Hey connections! We are building egocentric AI models for physical interaction mapping. We are urgently looking for annotated POV egocentric video data (kitchen, workshop, outdoors) matching custom scenarios. Drop an email at data-procurement@physicworld.ai or DM me!',
                'source_platform' => 'LinkedIn',
                'source_url' => 'https://linkedin.com/posts/physicworld-datasets-sourcing_pov-egocentric-video',
                'posted_at' => now()->subHours(4),
            ],
            [
                'content' => 'Textify AI is seeking full-text translation copywriters to annotating text outputs. Absolutely no multimodal or video datasets needed.',
                'source_platform' => 'LinkedIn',
                'source_url' => 'https://linkedin.com/posts/textify-ai-copywriter',
                'posted_at' => now()->subHours(18),
            ]
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Providers;

use App\Services\Ingestion\Contracts\IngestionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExaWebSearchService implements IngestionInterface
{
    public function fetchOpportunities(): array
    {
        $apiKey = env('EXA_API_KEY') ?: env('TAVILY_API_KEY');
        $keywords = config('hunter.keywords', []);

        if ($apiKey) {
            try {
                // Query Exa.ai or Tavily REST API to search forums and blogs
                $query = implode(' OR ', $keywords);
                $response = Http::timeout(15)
                    ->post('https://api.tavily.com/search', [
                        'api_key' => $apiKey,
                        'query' => $query . ' dataset procurement site:reddit.com OR site:hackernews.com',
                        'search_depth' => 'basic',
                    ]);

                if ($response->successful()) {
                    $results = $response->json()['results'] ?? [];
                    $posts = [];
                    foreach ($results as $result) {
                        $posts[] = [
                            'content' => sprintf("Title: %s\nSnippet: %s", $result['title'] ?? '', $result['content'] ?? ''),
                            'source_platform' => 'Web',
                            'source_url' => $result['url'] ?? 'https://exa.ai',
                            'posted_at' => now(),
                        ];
                    }
                    return $posts;
                }
            } catch (\Exception $e) {
                Log::error('ExaWebSearchService: Search failed. Fallback to mock: ' . $e->getMessage());
            }
        }

        // Mock Web Forum/Reddit Dataset Sourcing signals
        return [
            [
                'content' => '[Request] Looking to license talking-head datasets for AI Avatars. Need high-resolution video files of diverse speakers. Budget up to $50k depending on size and permissions. Contact us at growth@veedio.ai or WA +12135550199.',
                'source_platform' => 'Web',
                'source_url' => 'https://reddit.com/r/datasets/comments/veedio-video-avatars-request',
                'posted_at' => now()->subHours(2),
            ]
        ];
    }
}

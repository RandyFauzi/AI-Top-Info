<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Providers;

use App\Services\Ingestion\Contracts\IngestionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditOpportunityService implements IngestionInterface
{
    public function fetchOpportunities(): array
    {
        $keywords = [
            'looking for video dataset',
            'computer vision dataset',
            'need training data'
        ];

        $posts = [];
        $cutoffDate = now()->subDays(60)->timestamp;

        foreach ($keywords as $keyword) {
            $targetUrl = 'https://www.reddit.com/search.json';
            Log::info("RedditOpportunityService: Fetching target URL: {$targetUrl} with query keyword '{$keyword}'");

            try {
                // Hit Reddit open json search (disabling SSL verification for Laragon/curl compatibility)
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'AITopInfoAgent/1.0.0 (by /u/randyfauzi)'
                    ])
                    ->get($targetUrl, [
                        'q' => $keyword,
                        'sort' => 'new',
                        't' => 'month',
                        'limit' => 10
                    ]);

                Log::info("RedditOpportunityService: Received HTTP status: " . $response->status());
                Log::info("RedditOpportunityService: Raw response body snippet: " . substr($response->body(), 0, 500));

                if ($response->successful()) {
                    $children = $response->json()['data']['children'] ?? [];
                    foreach ($children as $child) {
                        $data = $child['data'] ?? [];
                        $createdUtc = $data['created_utc'] ?? null;

                        // Restrict to the last 60 days
                        if ($createdUtc && $createdUtc >= $cutoffDate) {
                            $permalink = $data['permalink'] ?? '';
                            $sourceUrl = !empty($permalink) ? 'https://www.reddit.com' . $permalink : 'https://www.reddit.com';

                            $title = $data['title'] ?? '';
                            $selftext = $data['selftext'] ?? '';
                            $content = "Title: {$title}\n\n{$selftext}";

                            $posts[] = [
                                'content' => $content,
                                'source_platform' => 'Web',
                                'source_url' => $sourceUrl,
                                'posted_at' => now()->setTimestamp((int) $createdUtc),
                            ];
                        }
                    }
                } else {
                    Log::error("RedditOpportunityService: Failed HTTP Request. Body: " . $response->body());
                }
                // Rate limit padding
                usleep(500000);
            } catch (\Exception $e) {
                Log::error("RedditOpportunityService: cURL / Connection failed for keyword '{$keyword}'. Error: " . $e->getMessage());
            }
        }

        return $posts;
    }
}

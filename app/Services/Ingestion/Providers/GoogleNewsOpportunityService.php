<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Providers;

use App\Services\Ingestion\Contracts\IngestionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleNewsOpportunityService implements IngestionInterface
{
    public function fetchOpportunities(): array
    {
        $posts = [];
        $url = 'https://news.google.com/rss/search?q=AI+video+dataset+startup+hiring+when:2m&hl=en-US&gl=US&ceid=US:en';

        Log::info("GoogleNewsOpportunityService: Fetching target URL: {$url}");

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($url);

            Log::info("GoogleNewsOpportunityService: Received HTTP status: " . $response->status());
            Log::info("GoogleNewsOpportunityService: Raw response body snippet: " . substr($response->body(), 0, 500));

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                if ($xml && isset($xml->channel->item)) {
                    foreach ($xml->channel->item as $item) {
                        $title = (string) $item->title;
                        $link = (string) $item->link;
                        $pubDate = (string) $item->pubDate;
                        $description = (string) $item->description;

                        $posts[] = [
                            'content' => "Title: {$title}\nDescription: {$description}",
                            'source_platform' => 'Web',
                            'source_url' => $link,
                            'posted_at' => !empty($pubDate) ? now()->parse($pubDate) : now(),
                        ];
                    }
                }
            } else {
                Log::error("GoogleNewsOpportunityService: Failed HTTP Request. Status: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("GoogleNewsOpportunityService: RSS fetch failed. Error: " . $e->getMessage());
        }

        return $posts;
    }
}

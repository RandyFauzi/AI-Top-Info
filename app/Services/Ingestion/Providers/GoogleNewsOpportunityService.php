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
        // when:2m limits results to the last 2 months
        $url = 'https://news.google.com/rss/search?q=AI+video+dataset+startup+hiring+when:2m&hl=en-US&gl=US&ceid=US:en';

        try {
            $response = Http::timeout(10)->get($url);

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
            }
        } catch (\Exception $e) {
            Log::error("GoogleNewsOpportunityService: RSS fetch failed: " . $e->getMessage());
        }

        return $posts;
    }
}

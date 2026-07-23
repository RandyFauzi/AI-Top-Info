<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NewsArticle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsFetcherService
{
    /**
     * Fetch the latest news articles for configured topics and keywords in English.
     *
     * @return int Number of successfully ingested articles
     */
    public function fetchLatestNews(): int
    {
        $topics = config('news_topics', []);
        $ingestedCount = 0;

        foreach ($topics as $category => $data) {
            $keywords = $data['keywords'] ?? [];
            foreach ($keywords as $keyword) {
                // hl=en-US & gl=US filters results to global English context
                $query = urlencode($keyword);
                $url = "https://news.google.com/rss/search?q={$query}&hl=en-US&gl=US&ceid=US:en";

                Log::info("NewsFetcherService: Fetching global RSS for [{$category}] - Keyword: '{$keyword}' URL: {$url}");

                try {
                    // Disable SSL verification for cURL safety in local Laragon environments
                    $response = Http::timeout(10)
                        ->withoutVerifying()
                        ->get($url);

                    if ($response->successful()) {
                        $xml = @simplexml_load_string($response->body());
                        if ($xml && isset($xml->channel->item)) {
                            foreach ($xml->channel->item as $item) {
                                $titleRaw = (string) $item->title;
                                $link = (string) $item->link;
                                $pubDate = (string) $item->pubDate;
                                $descriptionRaw = (string) $item->description;

                                // Clean up the title and extract source name
                                // Google News format: "Title of Article - Source Name"
                                $title = $titleRaw;
                                $sourceName = 'Global Source';
                                if (str_contains($titleRaw, ' - ')) {
                                    $parts = explode(' - ', $titleRaw);
                                    $sourceName = array_pop($parts);
                                    $title = implode(' - ', $parts);
                                }

                                // Remove HTML tags from Google News description
                                $summary = trim(strip_tags($descriptionRaw));

                                NewsArticle::updateOrCreate(
                                    ['url' => $link],
                                    [
                                        'topic_category' => $category,
                                        'title' => trim($title),
                                        'summary' => $summary ?: 'No description available.',
                                        'source_name' => trim($sourceName),
                                        'published_at' => !empty($pubDate) ? now()->parse($pubDate) : now(),
                                    ]
                                );

                                $ingestedCount++;
                            }
                        }
                    } else {
                        Log::error("NewsFetcherService: Failed to retrieve RSS. Status: " . $response->status());
                    }

                    // Brief pause to avoid rate limiting
                    usleep(200000);
                } catch (\Exception $e) {
                    Log::error("NewsFetcherService: Ingestion failed for keyword '{$keyword}'. Error: " . $e->getMessage());
                }
            }
        }

        return $ingestedCount;
    }
}

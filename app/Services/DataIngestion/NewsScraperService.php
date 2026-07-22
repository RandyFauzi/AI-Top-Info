<?php

declare(strict_types=1);

namespace App\Services\DataIngestion;

use App\Models\IntelligenceSignal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsScraperService
{
    public function scrape(): array
    {
        $apiKey = config('services.newsapi.key') ?: env('NEWS_API_KEY');
        $signals = [];

        if ($apiKey) {
            try {
                // Querying specific keywords relevant to CV, Generative Video, etc.
                $response = Http::timeout(10)->get('https://newsapi.org/v2/everything', [
                    'q' => '"computer vision" OR "generative video" OR "video AI" OR "autonomous driving" OR "AI model training"',
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => 10,
                    'apiKey' => $apiKey,
                ]);

                if ($response->successful()) {
                    $articles = $response->json()['articles'] ?? [];
                    foreach ($articles as $article) {
                        $content = sprintf(
                            "Title: %s\nDescription: %s\nContent: %s\nAuthor: %s",
                            $article['title'] ?? '',
                            $article['description'] ?? '',
                            $article['content'] ?? '',
                            $article['author'] ?? ''
                        );

                        $signals[] = IntelligenceSignal::create([
                            'source' => 'news',
                            'raw_content' => $content,
                            'extracted_url' => $article['url'] ?? null,
                            'published_at' => isset($article['publishedAt']) ? now()->parse($article['publishedAt']) : now(),
                        ]);
                    }
                    Log::info('NewsScraperService: Successfully ingested ' . count($signals) . ' articles.');
                    return $signals;
                }
            } catch (\Exception $e) {
                Log::error('NewsScraperService: API error, falling back to mock data. Error: ' . $e->getMessage());
            }
        }

        // High fidelity mock data for demonstration and local testing
        Log::info('NewsScraperService: Running with high fidelity mock data.');
        $mockArticles = [
            [
                'title' => 'Synthesia competitor Veedio AI raises $45M Series B for real-time generative video models',
                'description' => 'Veedio AI plans to use the fresh funding to build larger-scale generative video diffusion models, requiring massive video datasets and annotations.',
                'content' => 'Veedio AI, a leading video creation platform, announced a $45 million Series B round. CEO Jane Doe stated that the startup is investing heavily in licensing high-quality video footage and video captioning datasets for training next-gen AI avatars.',
                'url' => 'https://techcrunch.example.com/veedio-ai-funding',
                'publishedAt' => now()->subHours(2)->toIso8601String(),
            ],
            [
                'title' => 'VisionDrive secures partnerships with logistics giants to expand autonomous delivery drone fleet',
                'description' => 'VisionDrive is training deep neural networks for autonomous navigation in dense urban environments and is actively seeking multi-angle video training datasets.',
                'content' => 'VisionDrive, an autonomous drone logistics startup, announced new partnerships. The company needs massive real-world video training sets, including low-light and adverse weather driving/flying conditions, to train their computer vision models.',
                'url' => 'https://venturebeat.example.com/visiondrive-drones',
                'publishedAt' => now()->subHours(5)->toIso8601String(),
            ],
            [
                'title' => 'LexiWriter launches version 4 of their AI copywriting assistant powered by LLM technology',
                'description' => 'LexiWriter\'s new text assistant writes marketing copy and blog posts with superior grammatical precision.',
                'content' => 'LexiWriter has launched its latest AI copywriting application. The platform relies on fine-tuned text LLMs. The updates focus entirely on text prediction, grammar assistance, and multilingual copywriting features.',
                'url' => 'https://news.example.com/lexiwriter-v4',
                'publishedAt' => now()->subHours(12)->toIso8601String(),
            ]
        ];

        foreach ($mockArticles as $article) {
            $content = sprintf(
                "Title: %s\nDescription: %s\nContent: %s",
                $article['title'],
                $article['description'],
                $article['content']
            );

            // Make sure we don't duplicate existing ones in DB for testing
            $exists = IntelligenceSignal::where('source', 'news')
                ->where('raw_content', 'like', '%' . $article['title'] . '%')
                ->exists();

            if (!$exists) {
                $signals[] = IntelligenceSignal::create([
                    'source' => 'news',
                    'raw_content' => $content,
                    'extracted_url' => $article['url'],
                    'published_at' => now()->parse($article['publishedAt']),
                ]);
            }
        }

        return $signals;
    }
}

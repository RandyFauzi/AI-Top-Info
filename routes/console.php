<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\NewsFetcherService;
use App\Models\NewsArticle;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pipeline:test', function () {
    $this->info('Starting News Aggregator pipeline test...');

    // Clear previous runs
    $this->info('Clearing old news articles...');
    NewsArticle::truncate();

    // Trigger ingestion synchronously
    $this->info('Invoking NewsFetcherService...');
    $fetcher = app(NewsFetcherService::class);
    $count = $fetcher->fetchLatestNews();

    $this->info("Pipeline run completed! Ingested {$count} articles.");
    $this->info('-------------------------------------');
    
    $articles = NewsArticle::all();
    $this->info("Total Articles Aggregated in MySQL: " . $articles->count());
    
    foreach ($articles->take(5) as $art) {
        $this->line("Category: [" . $art->topic_category . "]");
        $this->line("Source: " . $art->source_name);
        $this->line("Title: " . $art->title);
        $this->line("URL: " . $art->url);
        $this->line("Published: " . $art->published_at);
        $this->line("-------------------------------------");
    }
})->purpose('Run full RSS news crawler pipeline validation');

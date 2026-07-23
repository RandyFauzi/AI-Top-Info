<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Ingestion\AggregatorManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunOpportunityHunterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AggregatorManager $aggregator): void
    {
        Log::info('RunOpportunityHunterJob: Starting web aggregation hunt...');

        $rawPosts = $aggregator->runHunt();

        Log::info('RunOpportunityHunterJob: Found ' . count($rawPosts) . ' raw postings. Dispatching evaluation jobs...');

        foreach ($rawPosts as $post) {
            ProcessRawDataWithGeminiJob::dispatch(
                $post['content'],
                $post['source_platform'],
                $post['source_url'],
                $post['posted_at']
            );
        }
    }
}

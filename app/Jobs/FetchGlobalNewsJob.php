<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\DataIngestion\DiscordIngestionService;
use App\Services\DataIngestion\LinkedInSignalService;
use App\Services\DataIngestion\NewsScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchGlobalNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        NewsScraperService $newsService,
        DiscordIngestionService $discordService,
        LinkedInSignalService $linkedinService
    ): void {
        Log::info('FetchGlobalNewsJob: Starting ingestion cycle...');

        // Ingest from all sources
        $newsSignals = $newsService->scrape();
        $discordSignals = $discordService->ingest();
        $linkedinSignals = $linkedinService->monitor();

        $totalCount = count($newsSignals) + count($discordSignals) + count($linkedinSignals);
        Log::info("FetchGlobalNewsJob: Ingestion cycle completed. Total signals ingested: {$totalCount}");
    }
}

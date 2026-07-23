<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Opportunity;
use App\Services\AIReasoning\GeminiAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRawDataWithGeminiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $rawContent;
    protected string $sourcePlatform;
    protected string $sourceUrl;
    protected $postedAt;

    public function __construct(string $rawContent, string $sourcePlatform, string $sourceUrl, $postedAt)
    {
        $this->rawContent = $rawContent;
        $this->sourcePlatform = $sourcePlatform;
        $this->sourceUrl = $sourceUrl;
        $this->postedAt = $postedAt;
    }

    public function handle(GeminiAnalysisService $analysisService): void
    {
        Log::info("ProcessRawDataWithGeminiJob: Evaluating posting from {$this->sourcePlatform}");

        $result = $analysisService->analyzeOpportunity($this->rawContent);

        // Save only if AI determines this is a relevant B2B dataset opportunity
        if (isset($result['is_relevant_opportunity']) && $result['is_relevant_opportunity'] === true) {
            
            Opportunity::updateOrCreate(
                ['source_url' => $this->sourceUrl],
                [
                    'title' => $result['title'] ?? 'Lead Opportunity',
                    'source_platform' => $result['source_platform'] ?? $this->sourcePlatform,
                    'summary' => $result['summary'] ?? '',
                    'extracted_contacts' => $result['contacts'] ?? null,
                    'posted_at' => $this->postedAt ?? now(),
                ]
            );

            Log::info("ProcessRawDataWithGeminiJob: Saved opportunity: " . ($result['title'] ?? ''));
        } else {
            Log::info("ProcessRawDataWithGeminiJob: Posting was filtered out by AI.");
        }
    }
}

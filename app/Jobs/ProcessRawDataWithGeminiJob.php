<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\IntelligenceSignal;
use App\Services\Scoring\LeadScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRawDataWithGeminiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected IntelligenceSignal $signal;

    public function __construct(IntelligenceSignal $signal)
    {
        $this->signal = $signal;
    }

    public function handle(LeadScoringService $scoringService): void
    {
        Log::info("ProcessRawDataWithGeminiJob: Processing signal ID {$this->signal->id}");

        $leadScore = $scoringService->processSignal($this->signal);

        Log::info("ProcessRawDataWithGeminiJob: Signal {$this->signal->id} scored {$leadScore->score} for company {$leadScore->company->name}");

        // Automatically trigger outreach generation job
        GenerateOutreachDraftJob::dispatch($leadScore);
    }
}

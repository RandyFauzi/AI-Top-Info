<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\LeadScore;
use App\Models\OutreachStrategy;
use App\Services\AIReasoning\GeminiAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateOutreachDraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected LeadScore $leadScore;

    public function __construct(LeadScore $leadScore)
    {
        $this->leadScore = $leadScore;
    }

    public function handle(GeminiAnalysisService $analysisService): void
    {
        $company = $this->leadScore->company;
        $signal = $this->leadScore->intelligenceSignal;

        Log::info("GenerateOutreachDraftJob: Drafting strategy for {$company->name}");

        $result = $analysisService->generateOutreach(
            $company->name,
            $company->description ?? '',
            $this->leadScore->intent_category,
            $this->leadScore->score,
            $this->leadScore->reasoning,
            $signal->raw_content
        );

        OutreachStrategy::create([
            'company_id' => $company->id,
            'target_persona' => $result['target_persona'] ?? 'CTO',
            'suggested_angle' => $result['suggested_angle'] ?? 'Value proposition based on recent AI updates.',
            'email_draft' => $result['email_draft'] ?? 'Hi, hope to discuss AI training data alignment.',
        ]);

        Log::info("GenerateOutreachDraftJob: Completed drafting for {$company->name}");
    }
}

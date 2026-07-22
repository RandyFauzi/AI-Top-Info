<?php

declare(strict_types=1);

namespace App\Services\Scoring;

use App\Models\Company;
use App\Models\IntelligenceSignal;
use App\Models\LeadScore;
use App\Services\AIReasoning\GeminiAnalysisService;

class LeadScoringService
{
    protected GeminiAnalysisService $analysisService;

    public function __construct(GeminiAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    public function processSignal(IntelligenceSignal $signal): LeadScore
    {
        // 1. Analyze the signal with Gemini
        $result = $this->analysisService->analyzeSignal($signal->raw_content);

        // 2. Find or create the company
        $company = Company::updateOrCreate(
            ['name' => $result['company_name']],
            [
                'domain' => $result['domain'] ?? null,
                'industry' => $result['industry'] ?? null,
                'description' => $result['description'] ?? null,
                'total_funding' => $result['total_funding'] ?? null,
                'contact_email' => $result['contacts']['email'] ?? null,
                'whatsapp_number' => $result['contacts']['whatsapp'] ?? null,
                'linkedin_url' => $result['contacts']['linkedin'] ?? null,
                'discord_url' => $result['contacts']['discord'] ?? null,
            ]
        );

        // Link the company to the signal
        $signal->update(['company_id' => $company->id]);

        // 3. Save the Lead Score
        $leadScore = LeadScore::create([
            'company_id' => $company->id,
            'intelligence_signal_id' => $signal->id,
            'score' => (int) $result['score'],
            'intent_category' => $result['intent_category'],
            'reasoning' => $result['reasoning'],
        ]);

        return $leadScore;
    }
}

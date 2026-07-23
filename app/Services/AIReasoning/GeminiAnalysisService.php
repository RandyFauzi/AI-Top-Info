<?php

declare(strict_types=1);

namespace App\Services\AIReasoning;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAnalysisService
{
    public function analyzeOpportunity(string $rawContent): array
    {
        try {
            $response = Http::timeout(30)
                ->post('http://127.0.0.1:8001/analyze-opportunity', [
                    'raw_content' => $rawContent,
                ]);

            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('GeminiAnalysisService: FastAPI returned non-successful code: ' . $response->status());
        } catch (\Exception $e) {
            Log::error('GeminiAnalysisService: FastAPI connection error. Error: ' . $e->getMessage());
        }

        // Mock data fallback removed. Throw exception to force pipeline to rely strictly on microservice.
        throw new \RuntimeException('FastAPI Ingestion Engine is offline or failed. Please check port 8001.');
    }
}

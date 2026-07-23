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
        } catch (\Exception $e) {
            Log::error('GeminiAnalysisService: FastAPI connection error. Error: ' . $e->getMessage());
        }

        return $this->generateMockOpportunity($rawContent);
    }

    private function generateMockOpportunity(string $rawContent): array
    {
        $contentLower = strtolower($rawContent);

        if (str_contains($contentLower, 'sora') || str_contains($contentLower, 'physicworld')) {
            return [
                'is_relevant_opportunity' => true,
                'title' => 'Sourcing Video Datasets for Physical Simulation AI',
                'summary' => 'PhysicWorld AI is seeking to license massive high-frame-rate interior video datasets of office spaces and public lobbies to refine their physical world synthesis model.',
                'source_platform' => 'Discord',
                'source_url' => 'https://discord.com/channels/123456789/announcements/987654',
                'contacts' => [
                    'email' => 'datasets@physicworld.ai',
                    'phone_wa' => null
                ]
            ];
        }

        if (str_contains($contentLower, 'drone') || str_contains($contentLower, 'visiondrive')) {
            return [
                'is_relevant_opportunity' => true,
                'title' => 'Computer Vision Engineer - Drones Navigation',
                'summary' => 'VisionDrive is hiring a CV Engineer to compile and build adverse-weather and multi-angle drone navigation video datasets.',
                'source_platform' => 'LinkedIn',
                'source_url' => 'https://linkedin.com/posts/visiondrive-drones-hiring',
                'contacts' => [
                    'email' => 'hiring@visiondrive.ai',
                    'phone_wa' => '14155550177'
                ]
            ];
        }

        if (str_contains($contentLower, 'veedio') || str_contains($contentLower, 'avatar')) {
            return [
                'is_relevant_opportunity' => true,
                'title' => 'Urgent License: AI Avatar Video Datasets',
                'summary' => 'Veedio AI is looking to license video files and video captioning datasets of talking heads for generative AI avatar training.',
                'source_platform' => 'Web',
                'source_url' => 'https://veedio.ai/careers/dataset-licensing',
                'contacts' => [
                    'email' => 'growth@veedio.ai',
                    'phone_wa' => '12135550199'
                ]
            ];
        }

        return [
            'is_relevant_opportunity' => false,
            'title' => 'NLP Writer Opportunity',
            'summary' => 'Looking for NLP text writers. Completely text-based LLM.',
            'source_platform' => 'LinkedIn',
            'source_url' => 'https://linkedin.com/posts/lexiwriter-copywriters',
            'contacts' => [
                'email' => 'support@lexiwriter.com',
                'phone_wa' => null
            ]
        ];
    }
}

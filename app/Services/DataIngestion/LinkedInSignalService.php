<?php

declare(strict_types=1);

namespace App\Services\DataIngestion;

use App\Models\IntelligenceSignal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInSignalService
{
    public function monitor(): array
    {
        $proxycurlKey = config('services.proxycurl.key') ?: env('PROXYCURL_API_KEY');
        $signals = [];

        if ($proxycurlKey) {
            try {
                // Monitor jobs matching 'computer vision' or 'video' for specific domains
                // Real-world implementation might poll LinkedIn Job Search endpoint via Proxycurl
                $response = Http::timeout(10)
                    ->withHeaders(['Authorization' => "Bearer $proxycurlKey"])
                    ->get('https://nubela.co/proxycurl/api/v2/jobs', [
                        'keyword' => 'Computer Vision Engineer',
                        'job_type' => 'full-time',
                        'limit' => 5,
                    ]);

                if ($response->successful()) {
                    $jobs = $response->json()['job_list'] ?? [];
                    foreach ($jobs as $job) {
                        $content = sprintf(
                            "Job Title: %s\nCompany: %s\nLocation: %s\nDescription: %s",
                            $job['title'] ?? '',
                            $job['company'] ?? '',
                            $job['location'] ?? '',
                            $job['description'] ?? ''
                        );

                        $signals[] = IntelligenceSignal::create([
                            'source' => 'linkedin',
                            'raw_content' => $content,
                            'extracted_url' => $job['job_url'] ?? null,
                            'published_at' => now(),
                        ]);
                    }
                    Log::info('LinkedInSignalService: Ingested ' . count($signals) . ' LinkedIn jobs.');
                    return $signals;
                }
            } catch (\Exception $e) {
                Log::error('LinkedInSignalService: API error, falling back. Error: ' . $e->getMessage());
            }
        }

        // Mock LinkedIn B2B Signal data
        Log::info('LinkedInSignalService: Running with high fidelity mock data.');
        $mockJobs = [
            [
                'title' => 'Senior Machine Learning Engineer - Video Understanding',
                'company' => 'AeroCam Systems',
                'location' => 'San Francisco, CA',
                'description' => 'AeroCam Systems is building intelligent surveillance drones. We are looking for a Senior ML Engineer to train our deep neural networks on multi-hour video streams. Role requires sourcing and prepping large-scale video database samples containing vehicle types, pedestrian motions, and custom hazard objects.',
                'url' => 'https://linkedin.example.com/jobs/aerocam-systems-ml',
            ],
            [
                'title' => 'Lead NLP Researcher (Text Models)',
                'company' => 'Textify AI',
                'location' => 'Austin, TX',
                'description' => 'Textify AI is seeking an expert NLP Researcher. You will lead development of our conversational chatbot model. Primary focus is text processing, tokenization efficiency, and prompt injection filters. Absolutely no multi-modal or video components planned for this cycle.',
                'url' => 'https://linkedin.example.com/jobs/textify-nlp',
            ]
        ];

        foreach ($mockJobs as $job) {
            $content = sprintf(
                "Job Title: %s\nCompany: %s\nLocation: %s\nDescription: %s",
                $job['title'],
                $job['company'],
                $job['location'],
                $job['description']
            );

            $exists = IntelligenceSignal::where('source', 'linkedin')
                ->where('raw_content', 'like', '%' . $job['company'] . '%')
                ->exists();

            if (!$exists) {
                $signals[] = IntelligenceSignal::create([
                    'source' => 'linkedin',
                    'raw_content' => $content,
                    'extracted_url' => $job['url'],
                    'published_at' => now()->subHours(10),
                ]);
            }
        }

        return $signals;
    }
}

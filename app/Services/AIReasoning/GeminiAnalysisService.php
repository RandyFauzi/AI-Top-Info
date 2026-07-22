<?php

declare(strict_types=1);

namespace App\Services\AIReasoning;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAnalysisService
{
    protected PromptManager $promptManager;

    public function __construct(PromptManager $promptManager)
    {
        $this->promptManager = $promptManager;
    }

    protected static array $resultsCache = [];

    public function analyzeSignal(string $rawContent): array
    {
        try {
            $response = Http::timeout(30)
                ->post('http://127.0.0.1:8001/analyze-company', [
                    'raw_content' => $rawContent,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $key = md5($rawContent);
                self::$resultsCache[$key] = $data;

                return [
                    'company_name' => $data['company_name'] ?? 'Unknown',
                    'domain' => $data['domain'] ?? null,
                    'industry' => $data['industry'] ?? null,
                    'description' => $data['description'] ?? null,
                    'total_funding' => $data['total_funding'] ?? null,
                    'score' => $data['score'] ?? 50,
                    'intent_category' => $data['intent_category'] ?? 'Other',
                    'contacts' => $data['contacts'] ?? null,
                    'reasoning' => $data['reasoning'] ?? '',
                ];
            }
        } catch (\Exception $e) {
            Log::error('GeminiAnalysisService: FastAPI connection error, falling back. Error: ' . $e->getMessage());
        }

        // Mock reasoning fallback if request fails or is offline
        return $this->generateMockAnalysis($rawContent);
    }

    public function generateOutreach(
        string $companyName,
        string $description,
        string $intentCategory,
        int $score,
        string $reasoning,
        string $rawSignal
    ): array {
        $key = md5($rawSignal);
        if (isset(self::$resultsCache[$key])) {
            $data = self::$resultsCache[$key];
            return [
                'target_persona' => $data['target_persona'] ?? 'CTO',
                'suggested_angle' => $data['suggested_angle'] ?? 'Value proposition based on recent AI updates.',
                'email_draft' => $data['email_draft'] ?? 'Hi, hope to discuss AI training data alignment.',
            ];
        }

        try {
            $response = Http::timeout(30)
                ->post('http://127.0.0.1:8001/analyze-company', [
                    'raw_content' => $rawSignal,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'target_persona' => $data['target_persona'] ?? 'CTO',
                    'suggested_angle' => $data['suggested_angle'] ?? 'Value proposition based on recent AI updates.',
                    'email_draft' => $data['email_draft'] ?? 'Hi, hope to discuss AI training data alignment.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('GeminiAnalysisService: FastAPI outreach fallback error: ' . $e->getMessage());
        }

        return $this->generateMockOutreach($companyName, $intentCategory, $score, $reasoning);
    }

    private function cleanJsonString(string $string): string
    {
        $string = trim($string);
        // Strip markdown codeblock backticks if present
        if (str_starts_with($string, '```')) {
            $string = preg_replace('/^```(?:json)?/i', '', $string);
            $string = preg_replace('/```$/', '', $string);
            $string = trim($string);
        }
        return $string;
    }

    private function generateMockAnalysis(string $rawContent): array
    {
        Log::info('GeminiAnalysisService: Simulating AI analysis (Mock mode).');

        if (stripos($rawContent, 'Veedio') !== false) {
            return [
                'company_name' => 'Veedio AI',
                'domain' => 'veedio.ai',
                'industry' => 'Generative AI & Video Creation',
                'description' => 'A video content creation platform raising funds to build generative AI avatars and diffusion models.',
                'total_funding' => '$45M',
                'score' => 95,
                'intent_category' => 'Generative Video',
                'contacts' => [
                    'email' => 'growth@veedio.ai',
                    'whatsapp' => '12135550199',
                    'linkedin' => 'https://linkedin.com/company/veedio-ai',
                    'discord' => 'https://discord.gg/veedio',
                ],
                'reasoning' => 'The company is actively training next-generation generative video diffusion models, which creates an immediate demand for extensive, high-quality licensed video training datasets to refine avatar realism and action execution.'
            ];
        }

        if (stripos($rawContent, 'VisionDrive') !== false) {
            return [
                'company_name' => 'VisionDrive',
                'domain' => 'visiondrive.ai',
                'industry' => 'Autonomous Vehicles & Logistics',
                'description' => 'An autonomous drone logistics startup expanding fleets and navigation systems.',
                'total_funding' => 'N/A',
                'score' => 90,
                'intent_category' => 'Computer Vision',
                'contacts' => [
                    'email' => 'team@visiondrive.ai',
                    'whatsapp' => '14155550177',
                    'linkedin' => 'https://linkedin.com/company/visiondrive',
                    'discord' => 'https://discord.gg/visiondrive',
                ],
                'reasoning' => 'The startup trains vision systems for spatial navigation and collision avoidance in multi-angle environments. They require highly specific annotated video datasets showcasing adverse weather conditions and low-light urban flights.'
            ];
        }

        if (stripos($rawContent, 'AeroCam') !== false) {
            return [
                'company_name' => 'AeroCam Systems',
                'domain' => 'aerocamsystems.com',
                'industry' => 'Defense & Security Tech',
                'description' => 'A builder of intelligent surveillance systems and drones mapping vehicle and pedestrian metrics.',
                'total_funding' => 'N/A',
                'score' => 88,
                'intent_category' => 'Computer Vision',
                'contacts' => [
                    'email' => 'contact@aerocamsystems.com',
                    'whatsapp' => '16505550144',
                    'linkedin' => 'https://linkedin.com/company/aerocam-systems',
                    'discord' => 'https://discord.gg/aerocam',
                ],
                'reasoning' => 'AeroCam is hiring ML Engineers specifically to process multi-hour security video streams and map vehicle types. Sourcing ready-to-train labeled security/vehicle video catalogs directly improves their time-to-market.'
            ];
        }

        if (stripos($rawContent, 'physicworld.ai') !== false || stripos($rawContent, 'Dr_SoraAI') !== false) {
            return [
                'company_name' => 'PhysicWorld AI',
                'domain' => 'physicworld.ai',
                'industry' => 'Physical Simulation AI',
                'description' => 'Developing next-generation AI physical world simulators and video synthesis architectures.',
                'total_funding' => '$12M',
                'score' => 96,
                'intent_category' => 'Generative Video',
                'contacts' => [
                    'email' => 'datasets@physicworld.ai',
                    'whatsapp' => '14085550133',
                    'linkedin' => 'https://linkedin.com/company/physicworld',
                    'discord' => 'https://discord.gg/physicworld',
                ],
                'reasoning' => 'PhysicWorld is explicitly seeking to license large libraries of video datasets for physical world simulation. Multi-angle interior videos of offices and public spaces are required, matching our catalog perfectly.'
            ];
        }

        if (stripos($rawContent, 'ChatGuru') !== false || stripos($rawContent, 'llama-support') !== false) {
            return [
                'company_name' => 'ChatGuru',
                'domain' => 'chatguru.example.com',
                'industry' => 'Customer Support AI',
                'description' => 'Creators of fine-tuned text LLM pipelines for customer support automation.',
                'total_funding' => 'N/A',
                'score' => 15,
                'intent_category' => 'Text LLM',
                'contacts' => [
                    'email' => 'hello@chatguru.example.com',
                    'whatsapp' => null,
                    'linkedin' => 'https://linkedin.com/company/chatguru',
                    'discord' => null,
                ],
                'reasoning' => 'ChatGuru is focused exclusively on text-based Llama models. They have no current or planned multi-modal or video components.'
            ];
        }

        if (stripos($rawContent, 'Textify') !== false) {
            return [
                'company_name' => 'Textify AI',
                'domain' => 'textify.example.com',
                'industry' => 'Natural Language Processing',
                'description' => 'Building conversational chatbot APIs and summarization services.',
                'total_funding' => '$5M',
                'score' => 12,
                'intent_category' => 'Text LLM',
                'contacts' => [
                    'email' => null,
                    'whatsapp' => null,
                    'linkedin' => 'https://linkedin.com/company/textify',
                    'discord' => null,
                ],
                'reasoning' => 'Textify AI relies on text tokenization and LLM models. Zero multi-modal context found.'
            ];
        }

        // Catch-all mock for text/NLP
        return [
            'company_name' => 'LexiWriter',
            'domain' => 'lexiwriter.com',
            'industry' => 'Software & NLP',
            'description' => 'A SaaS copywriting assistant focused on fine-tuned text LLM APIs.',
            'total_funding' => 'N/A',
            'score' => 25,
            'intent_category' => 'Text LLM',
            'contacts' => [
                'email' => 'support@lexiwriter.com',
                'whatsapp' => null,
                'linkedin' => null,
                'discord' => null,
            ],
            'reasoning' => 'LexiWriter is strictly a text generation utility. They have zero multi-modal or vision-related activities, resulting in a low score.'
        ];
    }

    private function generateMockOutreach(string $companyName, string $intentCategory, int $score, string $reasoning): array
    {
        Log::info('GeminiAnalysisService: Simulating AI outreach drafting (Mock mode).');

        $persona = $score >= 80 ? 'Lead Computer Vision Engineer' : 'Head of AI Product';
        $angle = "Help {$companyName} accelerate their {$intentCategory} training cycles with pre-labeled video datasets.";

        $email = <<<EMAIL
Subject: Accelerating {$companyName}'s model training datasets

Hi team,

I saw your recent updates on training newer {$intentCategory} models.

When building video models, sourcing high-fidelity multi-angle footage and correct annotations is usually the biggest bottleneck. We license pre-cleared, annotated video catalogs specifically formatted for custom CV training.

Would you be open to reviewing a free sample of 100 high-frame-rate spatial navigation clips?

Best,
The AI Top Info Datasets Team
EMAIL;

        return [
            'target_persona' => $persona,
            'suggested_angle' => $angle,
            'email_draft' => $email
        ];
    }
}

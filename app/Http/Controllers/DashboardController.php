<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Services\Ingestion\AggregatorManager;
use App\Services\AIReasoning\GeminiAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $platform = $request->input('platform');

        $query = Opportunity::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($platform) {
            $query->where('source_platform', $platform);
        }

        $opportunities = $query->orderBy('posted_at', 'desc')->get();

        // Calculate KPI metrics
        $totalCount = Opportunity::count();
        $linkedinCount = Opportunity::where('source_platform', 'LinkedIn')->count();
        $discordCount = Opportunity::where('source_platform', 'Discord')->count();
        $webCount = Opportunity::where('source_platform', 'Web')->count();

        return view('dashboard', compact('opportunities', 'totalCount', 'linkedinCount', 'discordCount', 'webCount', 'search', 'platform'));
    }

    public function triggerIngest()
    {
        // Fallback sync trigger using obsolete job
        \App\Jobs\RunOpportunityHunterJob::dispatchSync();

        return redirect()->route('dashboard')->with('status', 'Opportunity Hunter completed!');
    }

    public function runHunter(
        Request $request,
        AggregatorManager $aggregator,
        GeminiAnalysisService $analysisService
    ) {
        Log::info('DashboardController: Starting synchronous debug ingestion...');

        try {
            // Retrieve raw posts directly, bypassing background job queues
            $rawPosts = $aggregator->runHunt();
            Log::info('DashboardController: Aggregated raw postings: ' . count($rawPosts));

            $savedCount = 0;

            foreach ($rawPosts as $post) {
                $content = $post['content'] ?? '';
                
                Log::info("DashboardController: Sending raw content to Gemini analysis:\n" . $content);

                // Run analysis sequentially
                $result = $analysisService->analyzeOpportunity($content);

                Log::info("DashboardController: Gemini Response Payload: " . json_encode($result));

                if (isset($result['is_relevant_opportunity']) && $result['is_relevant_opportunity'] === true) {
                    Opportunity::updateOrCreate(
                        ['source_url' => $post['source_url']],
                        [
                            'title' => $result['title'] ?? 'Lead Opportunity',
                            'source_platform' => $result['source_platform'] ?? $post['source_platform'],
                            'summary' => $result['summary'] ?? '',
                            'extracted_contacts' => $result['contacts'] ?? null,
                            'posted_at' => $post['posted_at'] ?? now(),
                        ]
                    );
                    $savedCount++;
                } else {
                    Log::warning("DashboardController: Opportunity filtered out by Gemini. Content rejected:\n" . $content);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berita berhasil diperbarui! Saved {$savedCount} new opportunities."
            ]);
        } catch (\Exception $e) {
            Log::error('DashboardController: Synchronous ingestion failed. Error: ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menarik data: ' . $e->getMessage()
            ], 500);
        }
    }
}

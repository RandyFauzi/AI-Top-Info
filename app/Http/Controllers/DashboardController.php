<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Jobs\RunOpportunityHunterJob;
use Illuminate\Http\Request;

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
        // Run crawler and parser sync for instant dashboard update
        RunOpportunityHunterJob::dispatchSync();

        return redirect()->route('dashboard')->with('status', 'Opportunity Hunter completed! Scraped signals parsed by LangChain and updated successfully.');
    }

    public function runHunter(Request $request)
    {
        try {
            // Run live ingestion synchronously
            RunOpportunityHunterJob::dispatchSync();

            return response()->json([
                'status' => 'success',
                'message' => 'Berita berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menarik data: ' . $e->getMessage()
            ], 500);
        }
    }
}

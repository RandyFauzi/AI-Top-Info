<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\IntelligenceSignal;
use App\Models\LeadScore;
use App\Jobs\FetchGlobalNewsJob;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Search & filter parameters
        $search = $request->input('search');
        $minScore = $request->input('min_score');
        $source = $request->input('source');

        $query = Company::with(['latestLeadScore', 'latestOutreachStrategy', 'intelligenceSignals']);

        // Filter by company name
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by score threshold (needs checking the latest score relationship)
        if ($minScore) {
            $query->whereHas('latestLeadScore', function ($q) use ($minScore) {
                $q->where('score', '>=', (int) $minScore);
            });
        }

        // Filter by intelligence source
        if ($source) {
            $query->whereHas('intelligenceSignals', function ($q) use ($source) {
                $q->where('source', $source);
            });
        }

        // Retrieve and sort by highest score
        $companies = $query->get()->sortByDesc(function ($company) {
            return $company->latestLeadScore ? $company->latestLeadScore->score : 0;
        });

        // Compute general metrics
        $totalLeads = Company::count();
        $highValueLeads = Company::whereHas('latestLeadScore', function ($q) {
            $q->where('score', '>=', 80);
        })->count();
        $processedSignals = IntelligenceSignal::count();

        return view('dashboard', compact('companies', 'totalLeads', 'highValueLeads', 'processedSignals', 'search', 'minScore', 'source'));
    }

    public function triggerIngest()
    {
        // Run FetchGlobalNewsJob synchronously for instant feedback in UI
        FetchGlobalNewsJob::dispatchSync();

        return redirect()->route('dashboard')->with('status', 'Pipeline completed! Data successfully ingested and processed through Gemini API.');
    }
}

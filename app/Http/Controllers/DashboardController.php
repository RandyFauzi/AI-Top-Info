<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Services\NewsFetcherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = NewsArticle::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('topic_category', $category);
        }

        // Fetch articles sorted by newest publish date
        $articles = $query->orderBy('published_at', 'desc')->get();

        // Calculate KPI counters
        $totalCount = NewsArticle::count();
        $categoriesCount = NewsArticle::distinct('topic_category')->count('topic_category');
        $techCount = NewsArticle::where('topic_category', 'Tech & Development')->count();
        $financeCount = NewsArticle::where('topic_category', 'Corporate Finance & Tax')->count();
        $autoCount = NewsArticle::where('topic_category', 'Automotive')->count();

        // Pass categories list for dropdown filter
        $categories = ['Tech & Development', 'Corporate Finance & Tax', 'Automotive'];

        return view('dashboard', compact(
            'articles',
            'totalCount',
            'categoriesCount',
            'techCount',
            'financeCount',
            'autoCount',
            'search',
            'category',
            'categories'
        ));
    }

    public function runHunter(Request $request, NewsFetcherService $fetcher)
    {
        Log::info('DashboardController: Starting synchronous news fetching...');

        try {
            $ingestedCount = $fetcher->fetchLatestNews();

            return response()->json([
                'status' => 'success',
                'message' => "Berita berhasil diperbarui! Berhasil menarik {$ingestedCount} artikel terbaru."
            ]);
        } catch (\Exception $e) {
            Log::error('DashboardController: News ingestion failed. Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menarik data berita: ' . $e->getMessage()
            ], 500);
        }
    }
}

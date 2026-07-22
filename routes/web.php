<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Models\Company;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Mengambil perusahaan beserta relasi skor dan strateginya, diurutkan dari skor tertinggi
    $companies = Company::with(['leadScore', 'outreachStrategy'])
        ->join('lead_scores', 'companies.id', '=', 'lead_scores.company_id')
        ->orderBy('lead_scores.score', 'desc')
        ->select('companies.*') // Menghindari tabrakan ID
        ->get();

    return view('dashboard', compact('companies'));
})->name('dashboard');

Route::post('/ingest', [DashboardController::class, 'triggerIngest'])->name('dashboard.ingest');

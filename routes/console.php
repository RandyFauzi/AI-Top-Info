<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\FetchGlobalNewsJob;
use App\Models\Company;
use App\Models\IntelligenceSignal;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pipeline:test', function () {
    $this->info('Starting test B2B lead pipeline...');
    
    // Set queue connection to sync dynamically so background jobs run immediately
    config(['queue.default' => 'sync']);

    // Clear previous runs
    $this->info('Clearing old companies, signals, and scores...');
    Company::query()->delete();
    IntelligenceSignal::query()->delete();

    // Trigger ingestion job synchronously
    $this->info('Dispatching FetchGlobalNewsJob...');
    FetchGlobalNewsJob::dispatchSync();

    $this->info('Pipeline run completed!');
    $this->info('-------------------------------------');
    
    $companies = Company::with(['latestLeadScore', 'latestOutreachStrategy'])->get();
    $this->info("Total Companies Found: " . $companies->count());
    
    foreach ($companies as $company) {
        $this->line("Company: " . $company->name);
        $this->line("Domain: " . $company->domain);
        if ($company->latestLeadScore) {
            $this->line("  Score: " . $company->latestLeadScore->score);
            $this->line("  Category: " . $company->latestLeadScore->intent_category);
            $this->line("  Reasoning: " . $company->latestLeadScore->reasoning);
        }
        if ($company->latestOutreachStrategy) {
            $this->line("  Outreach Persona: " . $company->latestOutreachStrategy->target_persona);
            $this->line("  Email Draft Hook: " . substr($company->latestOutreachStrategy->email_draft, 0, 100) . "...");
        }
        $this->line("-------------------------------------");
    }
})->purpose('Run full ingestion and AI scoring pipeline validation');

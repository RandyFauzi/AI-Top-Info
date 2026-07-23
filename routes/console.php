<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\RunOpportunityHunterJob;
use App\Models\Opportunity;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pipeline:test', function () {
    $this->info('Starting Opportunity Hunter pipeline test...');
    
    // Set queue connection to sync dynamically so background jobs run immediately
    config(['queue.default' => 'sync']);

    // Clear previous runs
    $this->info('Clearing old opportunities...');
    Opportunity::query()->delete();

    // Trigger ingestion job synchronously
    $this->info('Dispatching RunOpportunityHunterJob...');
    RunOpportunityHunterJob::dispatchSync();

    $this->info('Pipeline run completed!');
    $this->info('-------------------------------------');
    
    $opportunities = Opportunity::all();
    $this->info("Total Opportunities Aggregated: " . $opportunities->count());
    
    foreach ($opportunities as $opp) {
        $this->line("Title: " . $opp->title);
        $this->line("Platform: " . $opp->source_platform);
        $this->line("URL: " . $opp->source_url);
        $this->line("Summary: " . $opp->summary);
        $this->line("Contacts: " . json_encode($opp->extracted_contacts));
        $this->line("-------------------------------------");
    }
})->purpose('Run full crawler and opportunity aggregator pipeline validation');

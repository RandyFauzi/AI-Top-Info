<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Opportunity;
use Illuminate\Console\Command;

class ClearOpportunitiesCommand extends Command
{
    protected $signature = 'opportunities:clear';
    protected $description = 'Truncate the opportunities database table to start fresh';

    public function handle(): void
    {
        $this->info('Clearing opportunities table...');
        Opportunity::truncate();
        $this->info('Database cleared successfully! Ready for live data.');
    }
}

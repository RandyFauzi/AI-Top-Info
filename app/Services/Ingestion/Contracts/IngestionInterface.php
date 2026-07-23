<?php

declare(strict_types=1);

namespace App\Services\Ingestion\Contracts;

interface IngestionInterface
{
    /**
     * Fetch raw postings/opportunities from the provider source.
     *
     * @return array Array of raw signal objects containing content, source_platform, source_url, etc.
     */
    public function fetchOpportunities(): array;
}

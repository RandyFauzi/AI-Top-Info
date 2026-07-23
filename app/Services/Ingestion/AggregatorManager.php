<?php

declare(strict_types=1);

namespace App\Services\Ingestion;

use App\Services\Ingestion\Providers\ApifyLinkedInService;
use App\Services\Ingestion\Providers\ExaWebSearchService;
use App\Services\Ingestion\Providers\DiscordBotListenerService;

class AggregatorManager
{
    protected ApifyLinkedInService $linkedin;
    protected ExaWebSearchService $exa;
    protected DiscordBotListenerService $discord;

    public function __construct(
        ApifyLinkedInService $linkedin,
        ExaWebSearchService $exa,
        DiscordBotListenerService $discord
    ) {
        $this->linkedin = $linkedin;
        $this->exa = $exa;
        $this->discord = $discord;
    }

    /**
     * Run the hunt across all platforms and return aggregated raw posts.
     *
     * @return array
     */
    public function runHunt(): array
    {
        $posts = [];

        // 1. Fetch from LinkedIn (Apify)
        $posts = array_merge($posts, $this->linkedin->fetchOpportunities());

        // 2. Fetch from Web/Forums (Exa/Tavily)
        $posts = array_merge($posts, $this->exa->fetchOpportunities());

        // 3. Fetch from Discord
        $posts = array_merge($posts, $this->discord->listenAndFetch());

        return $posts;
    }
}

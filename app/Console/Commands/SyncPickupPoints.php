<?php

namespace App\Console\Commands;

use App\Services\PickupPointSyncService;
use Illuminate\Console\Command;

class SyncPickupPoints extends Command
{
    protected $signature = 'pickup-points:sync';

    protected $description = 'Frissíti az átvételi pontok listáját a futárcégek nyilvános adatforrásaiból';

    public function handle(PickupPointSyncService $syncService): int
    {
        $counts = $syncService->syncAll();

        foreach ($counts as $carrier => $count) {
            $this->line(sprintf('%s: %d pont', strtoupper($carrier), $count));
        }

        return self::SUCCESS;
    }
}

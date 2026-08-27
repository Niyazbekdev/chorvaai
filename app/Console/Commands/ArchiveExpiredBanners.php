<?php

namespace App\Console\Commands;

use App\Services\BannerService;
use Illuminate\Console\Command;

class ArchiveExpiredBanners extends Command
{
    protected $signature   = 'banners:archive-expired';
    protected $description = 'Archive banners whose expiry date has passed';

    public function handle(BannerService $bannerService): int
    {
        $count = $bannerService->archiveExpired();
        $this->info("Archived {$count} expired banner(s).");
        return Command::SUCCESS;
    }
}

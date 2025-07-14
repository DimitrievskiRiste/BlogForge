<?php

namespace App\Console\Commands;

use App\Models\WebsiteSettings;
use App\Repositories\WebsiteSettingsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncWebsiteSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-website-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize website settings with cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $websiteSettingsRepo = new WebsiteSettingsRepository();
        $ttl = 24 * 30;
        WebsiteSettings::query()->orderBy('id', 'desc')->chunk(50, function(Collection $websiteSettings) use($ttl, $websiteSettingsRepo) {
            foreach($websiteSettings as $websiteSetting) {
                $websiteSettingsRepo->addOrUpdate($websiteSetting, $ttl);
            }
        });
    }
}

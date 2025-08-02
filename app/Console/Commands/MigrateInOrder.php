<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateInOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogforge:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate the tables in order to avoid any issues with foreign keys!';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $migrations = [
                "2025_07_06_122143_create_user_groups_table.php",
                "0001_01_01_000000_create_users_table.php",
                "0001_01_01_000001_create_cache_table.php",
                "0001_01_01_000002_create_jobs_table.php",
                "2025_07_06_133038_create_attachments_table.php",
                "2025_07_07_172558_create_user_logs_table.php",
                "2025_07_07_173136_create_website_settings_table.php",
                "2025_07_08_180343_create_user_attachments_table.php",
                "2025_07_09_170839_create_categories_table.php",
                "2025_07_14_230012_create_articles_table.php",
                "2025_07_18_174953_create_content_translations_table.php",
                "2025_07_24_124343_create_comments_table.php"
            ];
            $path = "database/migrations";
            $this->info("--- Creating default migrations, using $path as default migrations path");
            foreach($migrations as $migration)
            {
                $this->info("Creating migration table $migration");
                Artisan::call("migrate",["--path" => "$path/$migration"]);
            }
            $this->info("All migrations are successfully created!");
        } catch (\Exception $e){
            $this->error($e->getMessage());
            return 1;
        }
    }
}

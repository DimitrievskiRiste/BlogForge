<?php
namespace App\Installer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Database
{
    private static array $defaultTables = [
        "users",
        "cache",
        "jobs",
        "user_groups",
        "attachments",
        "user_logs",
        "website_settings",
        "user_attachments",
        "categories",
        "articles",
        "content_translations",
        "comments"
    ];
    /**
     * Get database tables
     * @return array
     */
    public function getDatabaseTables() :array
    {
        return Schema::getTables();
    }

    /**
     * Return bool true if db is empty, false otherwise
     * @return bool
     */
    public function isDbEmpty():bool {
        return (empty(self::getDatabaseTables()) ? true : false);
    }

    /**
     * Check DB connection
     * @return bool
     */
    public function checkConnection():bool{
        try {
            DB::connection()->getPdo();
            return true;
        } catch(\Exception $e) {
            Log::error($e);
            return false;
        }
    }
    public function createDBTables() :void
    {
        Artisan::call("blogforge:migrate");
    }
    public function getDefaultTables():array
    {
        return self::$defaultTables;
    }
    public static function seeder():Seeder {
        return new Seeder();
    }
}

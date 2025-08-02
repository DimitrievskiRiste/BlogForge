<?php
namespace App\Installer;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Database
{
    private static array $defaultTables = [
        "users",
        "cache",
        "cache_locks",
        "migrations",
        "sessions",
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
            return array_map('current', DB::select('SHOW TABLES'));
    }

    /**
     * Return bool true if db is empty, false otherwise
     * @return bool
     */
    public function isDbEmpty():bool {
        return (empty(self::getDatabaseTables()) ? true : false);
    }

    /**
     * Get DB connection
     * @return bool
     */
    public function getConnection():bool{
        try {
            self::getDatabaseTables();
            return true;
        } catch(\Exception $e) {
            Log::error($e);
            return false;
        }
        return false;
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

    /**
     * Drop all database tables. Return true on success, false on fail.
     * @return bool
     */
    public function dropTables():bool
    {
        try {
            Schema::dropAllTables();
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * Checks for missing tables. Return empty array or array with tables that are missing.
     * @return array
     */
    public function hasMissingTables() :array {
        $tables = [];
        $dbTables = self::getDatabaseTables();
        $defaultTables = self::getDefaultTables();
        foreach($defaultTables as $table) {
                if(!in_array($table, $dbTables)){
                    $tables[] = $table;
                }
        }
        return $tables;
    }
}

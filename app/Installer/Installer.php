<?php
namespace App\Installer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Installer
{
    protected static string $version = "v1.0";
    protected static int $versionId = 1000000;
    protected static string $fileName = "install.lock";
    protected static string $path = __DIR__."/..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."storage/app";

    /**
     * Get installer app version
     * @return string
     */
    public function getVersion(): string
    {
        return self::$version;
    }

    /**
     * Get app version id
     * @return int
     */
    public function getVersionId(): int
    {
        return self::$versionId;
    }


    /**
     * Check if application is already installed
     * @return bool
     */
    public function isInstalled() :bool
    {
        return (empty(self::getDatabaseTables()) ? true : false);
    }

    /**
     * Check if installation is locked. For security reasons do not delete install.lock file unless you want to re-install your app.
     * @return bool
     */
    public function isLocked():bool {
        return (file_exists(self::$path) && file_exists(self::$path."/".self::$fileName) ? true : false);
    }

    /**
     * Lock installation
     * @return void
     */
    public function lock():void {
        if(!self::isLocked()) {
            $date = date("d/m/Y H:i:s");
            $fileContent = <<<EOD
            Application is installed on $date.
            EOD;
            file_put_contents(self::$path."/".self::$fileName, $fileContent);
        }
    }

    /**
     * Instance of installer's db class
     * @return Database
     */
    public static function db() :Database {
        return new Database();
    }
    public static function env():DBEnvironment{
        return new DBEnvironment();
    }
}

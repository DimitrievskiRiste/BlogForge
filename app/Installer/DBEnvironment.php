<?php
namespace App\Installer;
use Illuminate\Support\Facades\Log;

class DBEnvironment
{
    protected static string $filePath = __DIR__."/..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.".env";
    protected static string $fileBackup = __DIR__."/..".DIRECTORY_SEPARATOR."Backups";

    /**
     * Generate backup of .env file.
     * @return bool
     */
    private function setEnvBackup():bool {
        try {
            $oldContent = file_get_contents(self::$filePath);
            if(!file_exists(self::$fileBackup)){
                mkdir(self::$fileBackup);
            }
            $date = date("dmYhis");
            file_put_contents(self::$fileBackup."/.env$date",$oldContent);
            return true;
        } catch(\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * Set .env file database information
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int $dbport
     * @return void
     */
    public function setDbData(string $host, string $user, string $password, int $dbport, string $dbname) :void {
        try {
            $hasBackup = self::setEnvBackup();
            if($hasBackup && !empty($host) && !empty($user) && !empty($password) && !empty($dbport)) {
                $keysFound = [
                    'DB_HOST' => false,
                    'DB_USERNAME' => false,
                    'DB_PASSWORD' => false,
                    'DB_PORT' => false,
                    'DB_DATABASE' => false
                ];
                // We have backup the original file, now we can safely proceed
                $lines = file(self::$filePath, FILE_IGNORE_NEW_LINES);
                foreach($lines as &$line) {
                    self::replaceLine($line, 'DB_HOST', $host, $keysFound);
                    self::replaceLine($line,'DB_USERNAME',$user,$keysFound);
                    self::replaceLine($line, 'DB_PASSWORD', $password, $keysFound);
                    self::replaceLine($line,'DB_DATABASE',$dbname, $keysFound);
                }
                if(!$keysFound["DB_HOST"]) {
                    $lines[] = "DB_HOST=$host";
                }
                if(!$keysFound['DB_USERNAME']) {
                    $lines[] = "DB_USERNAME=$user";
                }
                if(!$keysFound['DB_PASSWORD']){
                    $lines[] = "DB_PASSWORd=$password";
                }
                if(!$keysFound['DB_PORT']){
                    $lines[] = "DB_PORT=$dbport";
                }
                if(!$keysFound['DB_DATABASE']){
                    $lines[] = "DB_DATABASE=$dbname";
                }
                file_put_contents(self::$filePath, implode(PHP_EOL, $lines) . PHP_EOL);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
    private function replaceLine(&$line, $key, $value, &$keysFound) :void {
        if(strpos(trim($line), "$key=") === 0){
            $keysFound[$key] = true;
            $line = "$key=$value";
        }
    }
}

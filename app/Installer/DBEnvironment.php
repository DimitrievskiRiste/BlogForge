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
     * @param string $dbname
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
    private function replaceLine(&$line, $key, $value, &$keysFound) :void
    {
        if (strpos(trim($line), "$key=") === 0) {
            $keysFound[$key] = true;
            $line = "$key=$value";
        }
    }

    /**
     * Read specific .env key value. This function returns string if specific key is found or null if key not found.
     * @param string $key
     * @return string|null
     */
    private function getEnvKey(string $key) :string|null
    {
        try {
            $lines = file(self::$filePath, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), "$key=") === 0) {
                    return str_replace('"', '', $line);
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }

    /**
     * Check if DB_HOST .env variable is empty
     * @return bool
     */
    public function isDBHostEmpty():bool {
        return self::isDbInfoEmpty('DB_HOST');
    }

    /**
     * Check if DB_USERNAME .env variable is empty
     * @return bool
     */
    public function isDBUsernameEmpty():bool{
        return self::isDbInfoEmpty('DB_USERNAME');
    }

    /**
     * Check if DB_DATABASE .env variable is empty.
     * @return bool
     */
    public function isDBNameEmpty():bool{
        return self::isDbInfoEmpty('DB_DATABASE');
    }

    /**
     * Check if DB_PORT .env variable is empty
     * @return bool
     */
    public function isDBPortEmpty():bool{
        return self::isDbInfoEmpty('DB_PORT');
    }
    /**
     * Checks if some database information .env variable is empty.
     * @param string $key
     * @return bool
     */
    private function isDbInfoEmpty(string $key = 'DB_HOST'):bool {
        $dbInfo = self::getEnvKey($key);
        return !(!is_null($dbInfo) && ($value = explode('=', $dbInfo)) && array_key_exists(1, $value) && !empty($value[1]));
    }
}

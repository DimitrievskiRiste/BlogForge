<?php
namespace App;
class BlogForge
{
    public static string $version = "1.0";
    public static string $appName = "BlogForge";
    // To do for upgrade check
    public static string $callbackUrl = "";

    /**
     * Get application name
     * @return string
     */
    public static function getAppName(): string
    {
        return self::$appName;
    }

    /**
     * Get callback url for upgrade version check
     * @return string
     */
    public static function getCallbackUrl(): string
    {
        return self::$callbackUrl;
    }

    /**
     * Return current version
     * @return string
     */
    public static function getVersion(): string
    {
        return self::$version;
    }

}

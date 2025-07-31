<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class WebsiteSettingsRepository extends AbstractRepository
{
    private static $cacheKey = "website_settings";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class ContentTranslationsRepository extends AbstractRepository
{
    private static string $cacheKey = "content_translations";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

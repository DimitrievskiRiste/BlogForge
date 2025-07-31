<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class ArticlesRepository extends  AbstractRepository
{
    private static $cacheKey = "articles";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

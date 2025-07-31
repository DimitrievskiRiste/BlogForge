<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class CategoriesRepository extends AbstractRepository
{
    private static $cacheKey = "categories";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

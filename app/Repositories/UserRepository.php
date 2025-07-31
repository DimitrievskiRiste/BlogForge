<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserRepository extends AbstractRepository
{
    private static string $cacheKey = "users";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

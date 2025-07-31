<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserLogsRepository extends AbstractRepository
{
    private static string $cacheKey = "user_logs";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

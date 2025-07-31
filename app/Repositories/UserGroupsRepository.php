<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserGroupsRepository extends AbstractRepository
{
    private static string $cacheKey = "user_groups";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
        self::$cacheKey = $key;
    }
}

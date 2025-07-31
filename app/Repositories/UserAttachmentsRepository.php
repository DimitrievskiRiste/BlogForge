<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserAttachmentsRepository extends AbstractRepository
{
    private static string $cacheKey = "user_attachments";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key): void
    {
       self::$cacheKey = $key;
    }
}

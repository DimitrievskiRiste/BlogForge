<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class AttachmentsRepository extends AbstractRepository
{
    protected static $cacheKey = "attachments";
    public function getKey(): string
    {
        return self::$cacheKey;
    }
    public function setKey(string $key) :void
    {
        self::$cacheKey = $key;
    }
}

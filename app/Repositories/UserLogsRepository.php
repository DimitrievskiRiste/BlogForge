<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserLogsRepository extends AbstractRepository
{
    private $cacheKey = "user_logs";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

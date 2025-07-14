<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserGroupsRepository extends AbstractRepository
{
    private $cacheKey = "user_groups";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

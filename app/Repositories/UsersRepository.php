<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UsersRepository extends AbstractRepository
{
    private $cacheKey = "users";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

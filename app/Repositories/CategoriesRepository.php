<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class CategoriesRepository extends AbstractRepository
{
    private $cacheKey = "categories";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

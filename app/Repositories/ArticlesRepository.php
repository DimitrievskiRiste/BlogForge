<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class ArticlesRepository extends  AbstractRepository
{
    private $cacheKey = "articles";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

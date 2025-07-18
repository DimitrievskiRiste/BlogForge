<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class ContentTranslationsRepository extends AbstractRepository
{
    private $cacheKey = "content_translations";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class WebsiteSettingsRepository extends AbstractRepository
{
    private $cacheKey = "website_settings";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
        $this->cacheKey = $key;
    }
}

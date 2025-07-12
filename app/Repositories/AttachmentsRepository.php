<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class AttachmentsRepository extends AbstractRepository
{
    protected $cacheKey = "attachments";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key) :void
    {
        $this->cacheKey = $key;
    }
}

<?php
namespace App\Repositories;
use Riste\AbstractRepository;

class UserAttachmentsRepository extends AbstractRepository
{
    private $cacheKey = "user_attachments";
    public function getKey(): string
    {
        return $this->cacheKey;
    }
    public function setKey(string $key): void
    {
       $this->cacheKey = $key;
    }
}

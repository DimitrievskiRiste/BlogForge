<?php
 namespace App\Repositories;
 use Riste\AbstractRepository;
 class CommentsRepository extends AbstractRepository {
   private string $cacheKey = "_comments";
   public function getKey():string {
     return $this->cacheKey;
   }
   public function setKey(string $key):void {
     $this->cacheKey = $key;
   }
 }
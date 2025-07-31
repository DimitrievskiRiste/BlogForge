<?php
 namespace App\Repositories;
 use Riste\AbstractRepository;
 class CommentsRepository extends AbstractRepository {
   private static string $cacheKey = "_comments";
   public function getKey():string {
     return self::$cacheKey;
   }
   public function setKey(string $key):void {
     self::$cacheKey = $key;
   }
 }

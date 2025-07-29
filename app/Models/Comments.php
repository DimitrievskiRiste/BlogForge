<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @Property int $article_id
 * @property string $comment
 */
class Comments extends Model
{
    protected $table = "comments";
    protected $primaryKey = "id";
    protected $fillable = [
        "user_id",
        "article_id",
        "comment"
    ];
}

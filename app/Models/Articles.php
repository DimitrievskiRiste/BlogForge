<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property int $og_image
 * @property string $content
 * @property boolean $allow_comments
 * @property string $locale
 * @property int $parent_category_id
 * @property int $author
 * @property User $Author
 * @property Categories $Category
 */
class Articles extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'content',
        'allow_comments',
        'locale',
        "parent_category_id",
        "author"
    ];
    public function Author(): HasOne
    {
        return $this->hasOne(User::class,"author","id");
    }
    public function Category(): HasOne
    {
        return $this->hasOne(Categories::class,"parent_category_id","category_id");
    }
}

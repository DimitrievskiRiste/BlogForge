<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $article_id
 * @property string $content
 * @property string $locale
 */
class ContentTranslations extends Model
{
    protected $table = 'content_translations';
    protected $fillable = ['article_id', 'content', 'locale'];
    protected $primaryKey = 'id';
}

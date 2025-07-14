<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $category_id
 * @property string $category_name
 * @property string $category_slug
 * @property int $parent_category_id
 * @property bool $category_enabled
 * @property string $meta_keywords
 * @property string $meta_description
 * @property int $og_image
 */
class Categories extends Model
{
    protected $table = "categories";
    protected $primaryKey = "category_id";
    protected $fillable = [
        "category_name",
        "category_slug",
        "parent_category_id",
        "category_enabled",
        "og_image",
        "meta_keywords",
        "meta_description"
    ]
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $attachment_name
 * @property string $attachment_ext
 * @property string $mime_type
 * @property integer $size
 * @property string $attachment_path
 */
class Attachments extends Model
{
    protected $primaryKey = 'attachment_id';
    protected $table = 'attachments';
    protected $fillable = [
        'attachment_name',
        'attachment_ext',
        'mime_type',
        'size',
        'attachment_path'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $attachment_id
 * @property int $user_id
 */
class UserAttachments extends Model
{
    protected $table = 'user_attachments';
    protected $primaryKey = 'id';
    protected $fillable = ['attachment_id', 'user_id'];
}

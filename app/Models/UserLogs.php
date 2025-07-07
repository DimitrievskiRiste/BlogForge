<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id;
 * @property int $user_id
 * @property string $user_ip
 * @property string $log_text
 * @property string $user_device
 */
class UserLogs extends Model
{
    protected $table = 'user_logs';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id','user_ip','log_text','user_device'];
}

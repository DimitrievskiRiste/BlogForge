<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $website_name
 * @property int $website_logo
 * @property boolean $registration_enabled
 * @property boolean $registration_min_age
 * @property boolean $verify_email_address
 */
class WebsiteSettings extends Model
{
    protected $table = 'website_settings';
    protected $primaryKey = 'id';
    protected $fillable = ['website_name', 'website_logo', 'registration_enabled','registration_min_age','verify_email_address'];

}

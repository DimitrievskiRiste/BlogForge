<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $group_id
 * @property string $group_name
 * @property string $group_title
 * @property boolean $can_access_admincp
 * @property boolean $can_access_users
 * @property boolean $can_add_users
 * @property boolean $can_remove_users
 * @property boolean $can_access_categories
 * @property boolean $can_add_categories
 * @property boolean $can_remove_categories
 * @property boolean $can_edit_categories
 * @property boolean $can_access_articles
 * @property boolean $can_add_article
 * @property boolean $can_remove_article
 * @property boolean $can_edit_article
 * @property boolean $can_comment
 * @property boolean $can_delete_comments
 * @property boolean $can_delete_self_comment
 * @property boolean $can_edit_self_comment
 * @property boolean $can_change_settings
 * @property boolean $can_manage_admins
 * @property boolean $can_upload_attachments
 * @property boolean $can_remove_self_attachments
 * @property boolean $can_remove_attachments
 */
class UserGroups extends Model
{
    protected $primaryKey = "group_id";
    protected $table = "user_groups";
    protected $fillable = [
        "group_name",
        "group_title",
        "can_access_admincp",
        "can_access_users",
        "can_add_users",
        "can_remove_users",
        "can_access_categories",
        "can_add_categories",
        "can_remove_categories",
        "can_edit_categories",
        "can_access_articles",
        "can_add_article",
        "can_remove_article",
        "can_edit_article",
        "can_comment",
        "can_delete_comments",
        "can_delete_self_comment",
        "can_edit_self_comment",
        "can_change_settings",
        "can_manage_admins",
        "can_upload_attachments",
        "can_remove_self_attachments",
        "can_remove_attachments"
    ];
}

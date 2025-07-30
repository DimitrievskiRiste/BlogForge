<?php
namespace App\Installer;
use App\Models\User;
use App\Models\UserGroups;
use App\Repositories\UserGroupsRepository;
use Illuminate\Support\Facades\Log;
use Riste\AbstractRepository;

class Seeder
{
    public function hasAdminGroup() :bool {
        return UserGroups::query()->where('can_access_admincp','=', true)->where('can_manage_admins','=',true)->exists();
    }
    public function hasUserGroup() :bool {
        return UserGroups::query()->where('can_access_admincp','=', false)->exists();
    }

    /**
     * Create admin user group
     * @return void
     */
    public function createAdminGroup() :void
    {
        try {
            $group = new UserGroups();
            $group->group_name = "Administrators";
            $group->group_title = "Administrator";
            $group->can_add_groups = true;
            $group->can_remove_groups = true;
            $group->can_delete_comments = true;
            $group->can_remove_article = true;
            $group->can_delete_self_comment = true;
            $group->can_access_admincp = true;
            $group->can_edit_article = true;
            $group->can_edit_groups = true;
            $group->can_edit_categories = true;
            $group->can_add_article = true;
            $group->can_add_categories = true;
            $group->can_remove_categories = true;
            $group->can_change_settings = true;
            $group->can_upload_attachments = true;
            $group->can_access_articles = true;
            $group->can_access_categories = true;
            $group->can_access_users = true;
            $group->can_add_users = true;
            $group->can_remove_users = true;
            $group->can_comment = true;
            $group->can_manage_admins = true;
            $group->save();
            // Now let's add it in cache as well
            $this->groupRepo()->addOrUpdate($group);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Get Groups repository
     * @return AbstractRepository
     */
    protected function groupRepo() :AbstractRepository {
        return new UserGroupsRepository();
    }

    /**
     * Create default regular user group for members.
     * @return void
     */
    public function createUserGroup():void {
        try {
            $group = new UserGroups();
            $group->group_name = "Regular Users";
            $group->group_title = "Member";
            $group->can_comment = true;
            $group->can_access_admincp = false;
            $group->save();
            $this->groupRepo()->addOrUpdate($group);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Checks if there is already created admin user.
     * @return bool
     */
    public function hasAdminUser() :bool {
        $user = User::query()->with(['Group'])->first();
        return (!is_null($user) && !is_null($user->Group) && $user->Group->can_access_admincp && $user->Group->can_manage_admins ? true : false);
    }
}

<?php
namespace App\Installer;
use App\Models\User;
use App\Models\UserGroups;
use App\Models\WebsiteSettings;
use App\Repositories\UserGroupsRepository;
use App\Repositories\UserRepository;
use App\Repositories\WebsiteSettingsRepository;
use Illuminate\Support\Facades\Log;
use PHPUnit\Metadata\Group;
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
            self::groupRepo()->addOrUpdate($group);
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
            self::groupRepo()->addOrUpdate($group);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Checks if there is already created admin user.
     * @return bool
     */
    public function hasAdminUser() :bool
    {
        $user = User::query()->with(['Group'])->first();
        return (!is_null($user) && !is_null($user->Group) && $user->Group->can_access_admincp && $user->Group->can_manage_admins ? true : false);
    }
    public function createAdminAccount(string $name, string $email, string $password, string $birthDate, bool $emailVerified, string $lastName, string $token_password) :void
    {
        $admin = new User();
        $admin->name = $name;
        $admin->email = $email;
        $admin->password = password_hash($password, PASSWORD_BCRYPT);
        $admin->birth_date = $birthDate;
        $admin->email_verified = $emailVerified;
        $admin->last_name = $lastName;
        $admin->token_password = $token_password;
        $admin->group_id = self::getAdminGroup()->group_id;
        $admin->save();
        // let's add this user in cache as well
        self::usersRepo()->addOrUpdate($admin);
    }
    private function getAdminGroup():UserGroups
    {
        return UserGroups::query()->where('can_access_admincp', '=', true)->where('can_manage_admins', '=', true)
            ->first();
    }

    /**
     * Users repository instance
     * @return AbstractRepository
     */
    private function usersRepo():AbstractRepository
    {
        return new UserRepository();
    }

    /**
     * Website Settings Repository instance
     * @return AbstractRepository
     */
    private function settingsRepo():AbstractRepository {
        return new WebsiteSettingsRepository();
    }
    public function hasWebsiteSetting():bool {
        return (!is_null(WebsiteSettings::query()->first()) ? true : false);
    }
    public function createWebsiteSettings(string $websiteName, bool $registrationEnabled, bool $verifyEmail, int $regMinAge, int $websiteLogoAttachment) :void
    {
        $setting = new WebsiteSettings();
        $setting->website_name = $websiteName;
        $setting->registration_enabled = $registrationEnabled;
        $setting->verify_email_address = $verifyEmail;
        $setting->registration_min_age = $regMinAge;
        $setting->website_logo = $websiteLogoAttachment;
        $setting->save();
        // Lets now save the data in cache as well.
        self::settingsRepo()->addOrUpdate($setting);
    }
}

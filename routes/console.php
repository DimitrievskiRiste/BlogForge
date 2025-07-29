<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
/**
To prevent DRY i've made code reusable.
Schedule::command("cache:sync-users")->daily()->withoutOverlapping();
Schedule::command("cache:sync-website-settings")->daily()->withoutOverlapping();
Schedule::command("cache:sync-user-logs")->daily()->withoutOverlapping();
Schedule::command("cache:sync-user-groups")->daily()->withoutOverlapping();
Schedule::command("cache:sync-attachments")->daily()->withoutOverlapping();
Schedule::command("cache:sync-categories")->daily()->withoutOverlapping();
Schedule::command("cache:sync-user-attachments")->daily()->withoutOverlapping();
Schedule::command("cache:sync-articles")->daily()->withoutOverlapping();
*/
$models = [
    "Articles",
    "Attachments",
    "Categories",
    "User",
    "UserAttachments",
    "UserGroups",
    "UserLogs",
    "WebsiteSettings",
    "ContentTranslations",
    "Comments"
];
foreach($models as $model){
    Schedule::command("cache:sync-models $model")->daily()->withoutOverlapping();
}

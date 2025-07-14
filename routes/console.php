<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command("cache:sync-users")->daily()->withoutOverlapping();
Schedule::command("cache:sync-website-settings")->daily()->withoutOverlapping();
Schedule::command("cache:sync-user-logs")->daily()->withoutOverlapping();
Schedule::command("cache:sync-user-groups")->daily()->withoutOverlapping();
Schedule::command("cache:sync-attachments")->daily()->withoutOverlapping();
Schedule::command("cache:sync-categories")->daily()->withoutOverlapping();
Schedule::command("cache:sync-user-attachments")->daily()->withoutOverlapping();
Schedule::command("cache:sync-articles")->daily()->withoutOverlapping();

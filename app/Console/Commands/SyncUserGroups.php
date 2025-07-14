<?php

namespace App\Console\Commands;

use App\Models\UserGroups;
use App\Repositories\UserGroupsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncUserGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-user-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize user groups with cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userGroupsRepo = new UserGroupsRepository();
        $ttl = 24 * 30;
        UserGroups::query()->orderBy('group_id', 'desc')->chunk(50, function(Collection $userGroups) use($userGroupsRepo, $ttl){
           foreach($userGroups as $userGroup) {
               $userGroupsRepo->addOrUpdate($userGroup, $ttl);
           }
        });
    }
}

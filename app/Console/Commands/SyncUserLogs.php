<?php

namespace App\Console\Commands;

use App\Models\UserLogs;
use App\Repositories\UserLogsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncUserLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-user-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize user logs with cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userLogsRepo = new UserLogsRepository();
        $ttl = 24 * 30;
        UserLogs::query()->orderBy('id', 'desc')->chunk(50, function(Collection $userLogs) use($ttl, $userLogsRepo) {
           foreach($userLogs as $userLog) {
               $userLogsRepo->addOrUpdate($userLog, $ttl);
           }
        });
    }
}

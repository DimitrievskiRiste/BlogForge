<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\UsersRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize cache data with database on daily basis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = new User();
        $userRepo = new UsersRepository();
        $users->query()->orderBy('id','desc')->chunk(50, function(Collection $users) use($userRepo) {
            foreach($users as $user) {
                $userRepo->addOrUpdate($user);
            }
        });

    }
}

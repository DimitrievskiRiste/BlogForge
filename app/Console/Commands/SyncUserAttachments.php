<?php

namespace App\Console\Commands;

use App\Models\UserAttachments;
use App\Repositories\UserAttachmentsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncUserAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-user-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize user attachments with cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userAttachmentsRepo = new UserAttachmentsRepository();
        $ttl = 24 * 30;
        UserAttachments::query()->orderBy('id', 'desc')->chunk(50,function(Collection $userAttachments) use($userAttachmentsRepo, $ttl) {
            foreach($userAttachments as $userAttachment){
                $userAttachmentsRepo->addOrUpdate($userAttachment, $ttl);
            }
        });
    }
}

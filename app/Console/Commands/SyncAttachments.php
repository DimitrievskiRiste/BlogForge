<?php

namespace App\Console\Commands;

use App\Models\Attachments;
use App\Repositories\AttachmentsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize attachments with cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attachmentsRepo = new AttachmentsRepository();
        $ttl = 24 * 30;
        Attachments::query()->orderBy('attachment_id', 'desc')->chunk(50, function(Collection $attachments) use($attachmentsRepo, $ttl){
           foreach($attachments as $attachment) {
               $attachmentsRepo->addOrUpdate($attachment, $ttl);
           }
        });
    }
}

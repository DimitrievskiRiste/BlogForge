<?php

namespace App\Console\Commands;

use App\Models\Articles;
use App\Repositories\ArticlesRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $articlesRepo = new ArticlesRepository();
        $ttl = 24 * 30;
        Articles::query()->orderBy("id", "desc")->chunk(50, function(Collection $articles) use($ttl, $articlesRepo) {
           foreach($articles as $article) {
               $articlesRepo->addOrUpdate($article, $ttl);
           }
        });
    }
}

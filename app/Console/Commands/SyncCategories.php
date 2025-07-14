<?php

namespace App\Console\Commands;

use App\Models\Categories;
use App\Repositories\CategoriesRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes categories data with cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categoriesRepo = new CategoriesRepository();
        $ttl = 24 * 30;
        Categories::query()->orderBy('category_id','desc')->chunk(50,function(Collection $categories) use($categoriesRepo, $ttl){
            foreach($categories as $category) {
                $categoriesRepo->addOrUpdate($category, $ttl);
            }
        });
    }
}

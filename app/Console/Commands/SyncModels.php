<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Riste\AbstractRepository;

class SyncModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sync-models {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will synchronize database data with cached models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $arg = $this->argument("model");
            $repo = $this->loadRepo($arg);
            $this->info("Running sync for $arg model...");
            $model = $this->loadModel($arg);
            $ttl = 24 * 30;
            $model->query()->orderBy($model->getKeyName(),"desc")->chunk(50, function(Collection $items) use($repo, $ttl){
               foreach($items as $item){
                   $repo->addOrUpdate($item, $ttl);
               }
            });
            $this->info("Sync for $arg model has successfully completed!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
    protected function loadRepo($repoName) :AbstractRepository {
        $repo = new \ReflectionClass("\\App\\Repositories\\$repoName"."Repository");
        return $repo->newInstance();
    }
    protected function loadModel($model) :Model {
        $class = new \ReflectionClass("\\App\\Models\\$model");
        return $class->newInstance();
    }
}

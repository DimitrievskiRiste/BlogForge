<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogforge:make-repository {repositoryName?} {cacheKey?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate repository for model with specified cache key.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $repo = $this->argument('repositoryName');
            $cacheKey = $this->argument('cacheKey');
            if (empty($repo)) {
                $repo = $this->ask("Repository name");
            }
            if (empty($cacheKey)) {
                $cacheKey = $this->ask("Please provide cache key for the given repository");
            }
            // check if repository directory exists
            $path = __DIR__."/..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."Repositories";
            if(!file_exists($path)) {
                mkdir($path);
            }
            // file content
            if(!empty($repo) && !empty($cacheKey)) {
              $fileContent = <<<EOD
              <?php
               namespace App\Repositories;
               use Riste\AbstractRepository;
               class $repo extends AbstractRepository {
                 private string \$cacheKey = "$cacheKey";
                 public function getKey():string {
                   return \$this->cacheKey;
                 }
                 public function setKey(string \$key):void {
                   \$this->cacheKey = \$key;
                 }
               }
              EOD;
              file_put_contents("$path/$repo.php", $fileContent);
              $this->info("Repository $repo has been successfully created");
            }
        } catch(\Exception $e) {
            Log::error($e);
            $this->error($e->getFile()." On line ". $e->getLine());
            $this->warn($e->getMessage());
            return 1;
        }
    }
}

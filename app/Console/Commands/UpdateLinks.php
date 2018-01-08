<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Article\Create\Arguments;
use App\Helpscout\Domain\Values\Site;
use App\Helpscout\Domain\Services\Collection;
use HelpscoutApi\Response\Response;
use App\Helpscout\Domain\Services\Article;

class UpdateLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:links {collection} {path} {directoryNesting?} {removeFirstElement?} {categoryIndex?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses the html files updating links and then updating the documents on helpscout and in the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site              = new Site(env('SITE_ID'));
        $collectionService = new Collection();

        $args              = new Arguments(
            $this->argument('collection'),
            $this->argument('path'),
            $this->argument('directoryNesting'),
            $this->argument('removeFirstElement'),
            $this->argument('categoryIndex')
        );


        $collection = $collectionService->findInDatabase($args->getCollectionName());

        // create the articles
        $article = new Article();
        $article->updateLinks($args, $collectionService->handleCollection($collection, $args, $site));
    }
}

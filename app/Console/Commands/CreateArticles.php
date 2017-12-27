<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Article\Create\Arguments;
use App\Helpscout\Domain\Values\Site;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Domain\Services\Collection as CollectionService;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use HelpscoutApi\Response\Response;
use App\Helpscout\Domain\Services\Article;

class CreateArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:docs {collection} {path} {directoryNesting?} {removeFirstElement?} {categoryIndex?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a set of documents on Helpscout under a collection and set of categories based on folder name';

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
        $collectionService = new CollectionService();
        $collectionEntity  = new CollectionEntity();

        $args              = new Arguments(
            $this->argument('collection'),
            $this->argument('path'),
            $this->argument('directoryNesting'),
            $this->argument('removeFirstElement'),
            $this->argument('categoryIndex')
        );

        $collectionValue = null;
        $collectionFound = $collectionEntity->exists($args->getCollectionName());

        // handle collection
        if (is_null($collectionFound)) {
            $response        = $collectionService->create($site, 'private', $args->getCollectionName());
            $contents        = collect((new Response($response))->getContents()->collection);
            $collection      = $collectionEntity->new($contents);
            $collectionValue = new Collection($collection->collection_id, $collection->name);
            $collectionValue->setDbId($collection->id);
        } else {
            $collectionValue = new Collection($collectionFound->collection_id, $collectionFound->name);
            $collectionValue->setDbId($collectionFound->id);
        }

        // create the articles
        $article = new Article();
        $article->create($args, $collectionValue);
    }
}

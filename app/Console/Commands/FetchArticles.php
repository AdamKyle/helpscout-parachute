<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Collection;
use App\Helpscout\Article\Fetch\Collection\FetchFactory;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles {collectionId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all articles';

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
        $collectionId = $this->argument('collectionId');

        if (!is_null($collectionId)) {
            $collection  = Collection::where('id', $collectionId)->first();
            $article     = new FetchFactory($collection);

            $article->getFactoryInstance()->createFromCollection();
        } else {
            $collections = Collection::all();
            $article     = new FetchFactory($collections);

            $article->getFactoryInstance()->createFromCollections();
        }
    }
}

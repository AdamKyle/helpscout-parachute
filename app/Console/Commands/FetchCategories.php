<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Domain\Services\Category;


class FetchCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:categories {collection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store all categories';

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
        $collectionName = $this->argument('collection');

        if (!is_null($collectionName)) {
            $collectionValue = (new CollectionEntity())->findByName($collectionName);

            if (is_null($collectionValue)) {
                throw new \Exception($collectionName . ' was not found');
            }

            $this->manageCategory($collectionValue);
        } else {
            $collections = CollectionEntity::all();

            forEach($collections as $collection) {
                $collectionValue = new Collection($collection->collection_id);
                $collectionValue->setDbId($collection->id);

                $this->manageCategory($collectionValue);
            }
        }
    }

    protected function manageCategory(Collection $collectionValue) {
        $category   = new Category();
        $categories = $category->fetchAll($collectionValue);

        forEach($categories as $cat) {
            $categoryFound = CategoryEntity::where('name', $cat->name)->first();
            $collection    = new Collection($cat->collectionId);

            $collection->setDbId(CollectionEntity::where('collection_id', $cat->collectionId)->first()->id);

            if (is_null($categoryFound)) {
                (new CategoryEntity())->new(collect($cat), $collectionValue);
            } else {
                (new CategoryEntity())->updateExisting($categoryFound, collect($cat), $collectionValue);
            }
        }
    }
}

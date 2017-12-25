<?php

namespace App\Helpscout\Domain\Entities;

use App\Models\Article as ArticleModel;
use App\Helpscout\Domain\Values\Collection;
use Illuminate\Support\Collection as IlluminateCollection;

class Article extends ArticleModel {

    public function new(IlluminateCollection $article) {
        return $this::create([
            'article_id'     => $article['id'],
            'article_number' => $article['number'],
            'status'         => $article['status'],
            'name'           => $article['name'],
            'collection_id'  => $article['collectionId'],
            'content'        => $article['text'],
        ]);
    }
}

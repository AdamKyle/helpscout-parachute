<?php

namespace App\Helpscout\Domain\Services;

use \Pool as GuzzelPool;
use App\Helpscout\Request\Requests;
use HelpscoutApi\Response\Response;
use App\Helpscout\Domain\Services\Article;

class Pool {

    private $requests;

    private $article;

    public function __construct(Requests $requests, Article $article) {
        $this->requests = $requests;
        $this->article  = $article;
    }

    public function pool() {
        GuzzelPool::pool(
            $this->requests,
            function($reason, $index) {
                // Lets see what was in that request that failed:
                var_dump($this->requests->getRequests()[$index]->getBody()->getContents());

                throw new \Exception($reason);
            },
            function($response) {
                $contents     = (new Response($response))->getContents()->article;
                $this->article->createOrUpdate($contents);
            }
        );
    }
}

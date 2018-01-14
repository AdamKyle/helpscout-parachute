<?php
namespace App\Helpscout\Request;

use HelpscoutApi\Contracts\RequestPool;
use GuzzleHttp\Psr7\Request;

class Requests implements RequestPool {

    private $requests = [];

    private $concurrency;

    public function pushRequest(Request $request) {
        $this->requests[] = $request;
    }

    public function getRequests(): array {
        return $this->requests;
    }

    public function setConcurrency(int $concurrency) {
        $this->concurrency = $concurrency;
    }

    public function getConcurrency(): int {
        return $this->concurrency;
    }
}

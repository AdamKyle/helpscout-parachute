<?php

namespace App\Helpscout\Domain\Values;

use HelpscoutApi\Contracts\Collection as CollectionContract;

class Collection implements CollectionContract {

    private $id;

    private $dbId;

    public function __construct(string $id) {
        $this->id = $id;
    }

    public function getId(): string {
        return $this->id;
    }

    public function setDbId(int $id) {
        $this->dbId = $id;
    }

    public function getDbId(): int {
        return $this->dbId;
    }
}

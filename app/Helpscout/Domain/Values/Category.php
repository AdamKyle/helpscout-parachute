<?php

namespace App\Helpscout\Domain\Values;

use HelpscoutApi\Contracts\Category as CategoryContract;

class Category implements CategoryContract {

    private $categoryId;

    private $categoryNumber;

    private $id;

    public function __construct(string $categoryId) {
        $this->categoryId = $categoryId;
    }

    public function getId(): string {
        return $this->categoryId;
    }

    public function setNumber(string $number) {
        return $this->categoryNumber = $number;
    }

    public function getNumber(): string {
        return $this->categoryNumber;
    }

    public function setDbId(int $id) {
        $this->id = $id;
    }

    public function getDbId(): int {
        return $this->id;
    }
}

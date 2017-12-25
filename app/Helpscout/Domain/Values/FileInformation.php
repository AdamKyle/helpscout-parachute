<?php

namespace App\Helpscout\Domain\Values;

class FileInformation {

    private $fileName;

    private $category;

    private $contents;

    public function __construct(
        string $fileName,
        string $category,
        string $contents
    ) {
        $this->fileName = $fileName;
        $this->category = $category;
        $this->contents = $contents;
    }

    public function getFileName(): string {
        return $this->fileName;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getContents(): string {
        return $this->contents;
    }
}

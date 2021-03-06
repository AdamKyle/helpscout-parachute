<?php
namespace App\Helpscout\Article\Create;

class Arguments {

    private $collectionName;
    private $path;
    private $directoryNesting;
    private $removeFirstElement;
    private $categoryIndex;

    public function __construct(
        string $collectionName,
        string $path,
        string $directoryNesting = null,
        string $removeFirstElement = null,
        string $categoryIndex = null)
    {
        $this->collectionName     = $collectionName;
        $this->path               = $path;
        $this->directoryNesting   = is_null($directoryNesting) ? 0 : (int) $directoryNesting;
        $this->removeFirstElement = is_null($removeFirstElement) ? false : (bool) $removeFirstElement;
        $this->categoryIndex      = is_null($categoryIndex) ? 2 : $categoryIndex;
    }

    public function getCollectionName() {
        return $this->collectionName;
    }

    public function getPath() {
        return $this->path;
    }

    public function getDirectoryNesting() {
        return $this->directoryNesting;
    }

    public function shouldRemoveFirstElement() {
        return $this->removeFirstElement;
    }

    public function getCategoryIndex() {
        return $this->categoryIndex;
    }
}

<?php

namespace App\Helpscout\Files;

use League\HTMLToMarkdown\HtmlConverter;
use App\Helpscout\Domain\Values\FileInformation;
use \DOMDocument;

class Files {

    private $files              = [];
    private $filesFromDirectory = [];
    private $category;
    private $categoryIndex;

    private $tmp = [];

    public function __construct(int $categoryIndex = 2) {
        $this->categoryIndex = $categoryIndex;
    }

    public function getAllContents(string $path, int $directoryNesting = 0, bool $removeFirstElement = false) {
        $recursiveIteratorIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        $filesFromDirectory = [];
        $dirtoryBreakdown   = [];

        $this->filesFromDirectory($recursiveIteratorIterator, $removeFirstElement);

        forEach ($this->filesFromDirectory as $fileDetails) {
            $fileExtension = pathinfo($fileDetails, PATHINFO_EXTENSION);

            if ($fileExtension === 'md' || $fileExtension === 'html') {
                // Take off the Users/User/Path/Base ...
                $fileBreakdown = array_slice(explode('/', $fileDetails), 4);

                // Grab the file name based on extension
                if ($fileExtension === 'html') {
                    $fileName = explode('.html', end($fileBreakdown))[0];
                } else {
                    $fileName = explode('.md', end($fileBreakdown))[0];
                }

                // We now have the file name, so we can remove it.
                array_pop($fileBreakdown);

                if ($directoryNesting !== 0) {
                    // Set the category based on the directory nesting.
                    $this->setCategory($fileBreakdown, $directoryNesting);
                } else {
                    $categoryIndex = 2;

                    if (count($fileBreakdown) !== $this->categoryIndex) {
                        $categoryIndex = $this->categoryIndex;
                    }

                    // By default we take the second directory as the category unless specified
                    $this->category = $fileBreakdown[$categoryIndex] ?? 'untitled-category';
                }

                if ($fileName !== '') {
                    $this->groupArticlesByCategory($fileBreakdown);

                    // Get the contents
                    $contents = trim(file_get_contents($fileDetails));

                    // If the contents of a file are not empty:
                    if ($contents !== '') {

                        $this->files[] = new FileInformation($fileName, $this->category, $contents);
                    }
                }
            }
        }
    }

    public function getAllFiles() {
        return $this->files;
    }

    protected function setCategory(array $fileBreakdown, int $directoryNesting) {
        if (count($fileBreakdown) > ($directoryNesting - 1)) {
            $this->category = prev($fileBreakdown);
        } else {
            $this->category = end($fileBreakdown);
        }
    }

    protected function filesFromDirectory($recursiveIteratorIterator, bool $removeFirstElement = false) {
        forEach ($recursiveIteratorIterator as $fileInfo) {
            if($fileInfo->isDir()){
                continue;
            }

            $this->filesFromDirectory[] = $fileInfo->getPathname();
        }

        if ($removeFirstElement) {
            array_shift($this->filesFromDirectory);
        }
    }

    protected function groupArticlesByCategory(array $fileBreakdown) {
        if (count($this->files) > 0) {
            $hasCategory = $this->checkIfCategoryExists($fileBreakdown);

            if ($hasCategory) {
                $this->category = $hasCategory;
            }
        }
    }

    protected function checkIfCategoryExists($collection) {
        if (count($this->files) < 1) {
            return false;
        }

        forEach($this->files as $file) {
            if (in_array($file->getCategory(), $collection)) {
                return $file->getCategory();
            }
        }

        return false;
    }
}

<?php

namespace App\Helpscout\Files;

use League\HTMLToMarkdown\HtmlConverter;
use App\Helpscout\Domain\Values\FileInformation;

class Files {

    private $files              = [];
    private $filesFromDirectory = [];
    private $category;

    private $tmp = [];

    public function getAllContents(string $path, int $directoryNesting = 0, bool $removeFirstElement = false) {
        $recursiveIteratorIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        $filesFromDirectory = [];
        $dirtoryBreakdown   = [];

        $this->filesFromDirectory($recursiveIteratorIterator, $removeFirstElement);

        forEach ($this->filesFromDirectory as $fileDetails) {
            if (pathinfo($fileDetails, PATHINFO_EXTENSION) === 'md') {
                // Take off the Users/User/Path/Base ...
                $fileBreakdown = array_slice(explode('/', $fileDetails), 4);
                $fileName      = explode('.md', end($fileBreakdown))[0];

                // We now have the file name, so we can remove it.
                array_pop($fileBreakdown);

                if ($directoryNesting !== 0) {
                    // Set the category based on the directory nesting.
                    $this->setCategory($fileBreakdown, $directoryNesting);
                } else {
                    // By default we take the second directory as the category
                    $this->category = $fileBreakdown[2] ?? 'manuals';
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

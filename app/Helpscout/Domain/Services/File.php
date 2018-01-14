<?php

namespace App\Helpscout\Domain\Services;

use App\Helpscout\Files\Files;
use App\Helpscout\Article\Create\Arguments;

class File {

    public function fetchAllFiles(Arguments $args) {
        $files = new Files($args->getCategoryIndex());

        $files->getAllContents(
            $args->getPath(),
            $args->getDirectoryNesting(),
            $args->shouldRemoveFirstElement()
        );

        return $files->getAllFiles();
    }
}

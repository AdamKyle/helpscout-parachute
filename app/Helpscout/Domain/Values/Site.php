<?php

namespace App\Helpscout\Domain\Values;

use HelpscoutApi\Contracts\Site as SiteContract;

class Site implements SiteContract {
    private $siteId;

    public function __construct(string $siteId) {
        $this->siteId = $siteId;
    }

    public function getId(): string {
        return $this->siteId;
    }
}

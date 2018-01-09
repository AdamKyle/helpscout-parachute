<?php

namespace App\Helpscout\Domain\Values;

class ArticleLink {

    private $attribute;
    private $attributeValue;

    public function __construct(string $attribute, string $attributeValue) {
        $this->attribute      = $attribute;
        $this->attributeValue = $attributeValue;
    }

    public function getAttribute(): string {
        return $this->attribute;
    }

    public function getAttributeValue(): string {
        return $this->attributeValue;
    }
}

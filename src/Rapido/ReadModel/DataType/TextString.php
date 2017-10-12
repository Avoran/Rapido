<?php

namespace Avoran\Rapido\ReadModel\DataType;

final class TextString implements FieldDataType
{
    private $maxLength;
    public function getMaxLength() { return $this->maxLength; }

    public function __construct($maxLength = null)
    {
        $this->maxLength = $maxLength;
    }
}

<?php

namespace Avoran\Rapido\ReadModel\DataType;

final class Decimal implements FieldDataType
{
    private $totalDigits;
    public function getTotalDigits() { return $this->totalDigits; }

    private $fractionalDigits;
    public function getFractionalDigits() { return $this->fractionalDigits; }

    public function __construct($totalDigits, $fractionalDigits)
    {
        if ($totalDigits <= $fractionalDigits)
            throw new \InvalidArgumentException("'totalDigits' ({$totalDigits}) has to be larger than 'fractionalDigits' ({$fractionalDigits})");

        $this->totalDigits = $totalDigits;
        $this->fractionalDigits = $fractionalDigits;
    }
}

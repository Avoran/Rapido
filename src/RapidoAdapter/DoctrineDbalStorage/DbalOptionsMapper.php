<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Avoran\Rapido\ReadModel\DataType\Decimal;
use Avoran\Rapido\ReadModel\DataType\FieldDataType;

class DbalOptionsMapper
{
    public function mapReadModelToDbalOptions(FieldDataType $fieldDataType)
    {
        $options = ['notnull' => false];

        switch ($fieldDataType) {
            case $fieldDataType instanceof Decimal:
                $options = array_merge($options, ['scale' => $fieldDataType->getFractionalDigits(), 'precision' => $fieldDataType->getTotalDigits()]);
                break;
        }

        return $options;
    }
}

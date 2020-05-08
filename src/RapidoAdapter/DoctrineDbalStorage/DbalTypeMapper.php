<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Doctrine\DBAL\Types\Type;
use Avoran\Rapido\ReadModel\DataType\Boolean;
use Avoran\Rapido\ReadModel\DataType\DateTime;
use Avoran\Rapido\ReadModel\DataType\Decimal;
use Avoran\Rapido\ReadModel\DataType\FieldDataType;
use Avoran\Rapido\ReadModel\DataType\Integer;
use Avoran\Rapido\ReadModel\DataType\TextString;
use Doctrine\DBAL\Types\Types;

class DbalTypeMapper
{
    public function mapReadModelToDbalType(FieldDataType $fieldDataType)
    {
        switch ($fieldDataType) {
            case $fieldDataType instanceof Integer:
                return Type::getType(Types::INTEGER);
            case $fieldDataType instanceof Decimal:
                return Type::getType(Types::DECIMAL);
            case $fieldDataType instanceof Boolean:
                return Type::getType(Types::BOOLEAN);
            case $fieldDataType instanceof TextString:
                return $fieldDataType->getMaxLength() && $fieldDataType->getMaxLength() < 256
                    ? Type::getType(Types::STRING)
                    : Type::getType(Types::TEXT)
                ;
            case $fieldDataType instanceof DateTime:
                return Type::getType(Types::DATETIME_MUTABLE);
            default:
                throw new \InvalidArgumentException("Could not find a suitable data type");
        }
    }
}

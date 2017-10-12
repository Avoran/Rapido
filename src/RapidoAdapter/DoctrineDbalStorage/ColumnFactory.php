<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Doctrine\DBAL\Schema\Column;
use Avoran\Rapido\ReadModel\DataType\FieldDataType;

class ColumnFactory
{
    private $dbalTypeMapper;
    private $dbalOptionsMapper;

    public function __construct(DbalTypeMapper $dbalTypeMapper, DbalOptionsMapper $dbalOptionsMapper)
    {
        $this->dbalTypeMapper = $dbalTypeMapper;
        $this->dbalOptionsMapper = $dbalOptionsMapper;
    }

    public function createColumn(FieldDataType $dataType, $id)
    {
        return new Column(
            $id,
            $this->dbalTypeMapper->mapReadModelToDbalType($dataType),
            $this->dbalOptionsMapper->mapReadModelToDbalOptions($dataType)
        );
    }
}

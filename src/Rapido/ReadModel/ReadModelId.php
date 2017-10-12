<?php

namespace Avoran\Rapido\ReadModel;

use Avoran\Rapido\ReadModel\DataType\FieldDataType;

final class ReadModelId
{
    private $dataType;
    public function getDataType() { return $this->dataType; }

    public function __construct(FieldDataType $dataType)
    {
        $this->dataType = $dataType;
    }
}

<?php

namespace Avoran\Rapido\ReadModel;

use Avoran\Rapido\ReadModel\DataType\FieldDataType;

final class ReadModelField
{
    private $id;
    public function getId() { return $this->id; }

    private $dataType;
    public function getDataType() { return $this->dataType; }

    private $index;
    public function useIndex() { return $this->index; }

    public function __construct($id, FieldDataType $dataType, $index = false)
    {
        $this->id = $id;
        $this->dataType = $dataType;
        $this->index = $index;
    }
}

<?php

namespace Avoran\Rapido\ReadModel;

final class ReadModelConfiguration
{
    private $name;
    public function getName() { return $this->name; }

    private $id;
    public function getId() { return $this->id; }

    /** @var ReadModelField[] */
    private $fields;
    public function getFields() { return $this->fields; }

    private $recordFactory;
    private $allRecords;

    public function __construct($name, ReadModelId $id, array $fields, callable $recordFactory, callable $allRecords)
    {
        $this->name = $name;
        $this->id = $id;
        $this->fields = $fields;
        $this->recordFactory = $recordFactory;
        $this->allRecords = $allRecords;
    }

    public function getAllRecords()
    {
        $allRecords = $this->allRecords;
        foreach ($allRecords() as $recordData)
            yield $recordData;
    }

    /** @return Record */
    public function createRecord($recordData)
    {
        $recordFactory = $this->recordFactory;
        return $recordFactory($recordData);
    }
}

<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Avoran\Rapido\ReadModel\ReadModelConfiguration;
use Avoran\Rapido\ReadModel\ReadModelField;
use Avoran\Rapido\ReadModel\StorageWriter;

class DoctrineDbalStorageWriter implements StorageWriter
{
    private $connection;
    private $checkedSchema = [];
    private $schemaSynchronizer;
    private $tableNameGenerator;
    private $idColumnName;
    private $dbalTypeMapper;

    public function __construct
    (
        Connection $connection,
        SchemaSynchronizer $tableGenerator,
        NameGenerator $tableNameGenerator,
        $idColumnName,
        DbalTypeMapper $dbalTypeMapper
    )
    {
        $this->connection = $connection;
        $this->schemaSynchronizer = $tableGenerator;
        $this->tableNameGenerator = $tableNameGenerator;
        $this->idColumnName = $idColumnName;
        $this->dbalTypeMapper = $dbalTypeMapper;
    }

    public function writeRecord(ReadModelConfiguration $metadata, $recordData)
    {
        if (!isset($this->checkedSchema[$metadata->getName()])) {
            $this->schemaSynchronizer->ensureTableExists($metadata);
            $this->checkedSchema[$metadata->getName()] = true;
        }

        $tableName = $this->tableNameGenerator->generate($metadata->getName());
        $record = $metadata->createRecord($recordData);

        $types = array_map(function (ReadModelField $field) {
            return $this->dbalTypeMapper->mapReadModelToDbalType($field->getDataType());
        }, $metadata->getFields());

        $rowData = array_merge([$this->idColumnName => $record->getId()], $record->getData());
        $idType = $this->dbalTypeMapper->mapReadModelToDbalType($metadata->getId()->getDataType());

        try {
            $this->connection->insert($tableName, $rowData, array_merge([$idType], $types));
        } catch (UniqueConstraintViolationException $e) {
            $this->connection->update($tableName, $record->getData(), [$this->idColumnName => $record->getId()], $types);
        }
    }
}

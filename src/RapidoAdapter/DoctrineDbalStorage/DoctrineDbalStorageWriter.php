<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Avoran\Rapido\ReadModel\ReadModelConfiguration;
use Avoran\Rapido\ReadModel\ReadModelField;
use Avoran\Rapido\ReadModel\StorageWriter;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

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

        $rowData = array_merge([$this->idColumnName => $record->getId()], $record->getData());

        $idType = $this->dbalTypeMapper->mapReadModelToDbalType($metadata->getId()->getDataType());
        $types = array_merge([$idType], array_map(function (ReadModelField $field) {
            return $this->dbalTypeMapper->mapReadModelToDbalType($field->getDataType());
        }, $metadata->getFields()));

        $columns = $values = $insertSet = $updateSet = [];

        foreach ($rowData as $columnName => $value) {
            $columns[]   = $columnName;
            $values[]    = $value;
            $insertSet[] = '?';
            $updateSet[] = "$columnName = ?";
        }

        $columnsStr = implode(', ', $columns);
        $insertParams = implode(', ', $insertSet);
        $updateParams = implode(', ', $updateSet);

        if ($this->connection->getDatabasePlatform() instanceof MySqlPlatform) {
            $query = "INSERT INTO $tableName ($columnsStr) VALUES ($insertParams) ON DUPLICATE KEY UPDATE $updateParams";
        } elseif ($this->connection->getDatabasePlatform() instanceof SqlitePlatform) {
            $query = "INSERT INTO $tableName ($columnsStr) VALUES ($insertParams) ON CONFLICT($this->idColumnName) DO UPDATE SET $updateParams";
        } else {
            throw new InvalidArgumentException('This database platform is not supported by Rapido');
        }

        $this->connection->executeQuery($query, array_merge($values, $values), array_merge($types, $types));
    }
}

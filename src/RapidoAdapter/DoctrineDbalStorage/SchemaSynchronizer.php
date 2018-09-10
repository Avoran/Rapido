<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Avoran\Rapido\ReadModel\DataType\DateTime;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Avoran\Rapido\ReadModel\ReadModelConfiguration;
use Avoran\Rapido\ReadModel\ReadModelField;

class SchemaSynchronizer
{
    private $schemaManager;
    private $schemaComparator;
    private $tableNameGenerator;
    private $idColumnName;
    private $columnFactory;

    public function __construct
    (
        AbstractSchemaManager $schemaManager,
        Comparator $schemaComparator,
        NameGenerator $tableNameGenerator,
        $idColumnName,
        ColumnFactory $columnFactory
    )
    {
        $this->schemaManager = $schemaManager;
        $this->schemaComparator = $schemaComparator;
        $this->tableNameGenerator = $tableNameGenerator;
        $this->idColumnName = $idColumnName;
        $this->columnFactory = $columnFactory;
    }

    public function ensureTableExists(ReadModelConfiguration $metadata)
    {
        $table = $this->generateTable($metadata);
        $table->setPrimaryKey([$this->idColumnName]);

        $this->updateTable($table);

        if (!$metadata->generateSnapshot()) return;

        $table = $this->generateTable($metadata, true);
        $table->setPrimaryKey([$this->idColumnName, $metadata->getSnapshotColumnName()]);

        $this->updateTable($table);
    }

    private function generateTable(ReadModelConfiguration $metadata, $snapshot = false)
    {
        $predefinedColumns = [$this->columnFactory->createColumn($metadata->getId()->getDataType(), $this->idColumnName)];
        $name = $this->tableNameGenerator->generate($metadata->getName());
        $indices = [new Index(sprintf('IDX_%s', $this->idColumnName), $this->idColumnName, false, true)];

        if ($snapshot) {
            $predefinedColumns[] = $this->columnFactory->createColumn(new DateTime(), $metadata->getSnapshotColumnName());
            $name = $this->tableNameGenerator->generateWithSuffix($metadata->getName());
            $indices[] = new Index(sprintf('IDX_%s', $metadata->getSnapshotColumnName()), $metadata->getSnapshotColumnName());
        }

        return new Table($name,
            array_merge($predefinedColumns, array_map(function(ReadModelField $field) {
                return $this->columnFactory->createColumn($field->getDataType(), $field->getId());
            }, $metadata->getFields())),
            $indices
        );
    }

    private function updateTable(Table $table)
    {
        $existingTables = array_filter(
            $this->schemaManager->listTables(),
            function(Table $existingTable) use ($table) { return $table->getName() == $existingTable->getName(); }
        );

        if (count($existingTables) > 1)
            throw new \UnexpectedValueException(sprintf("Multiple tables with the name '%s' exist.", $table->getName()));

        if (count($existingTables) == 1) {
            $tableDiff = $this->schemaComparator->diffTable(current($existingTables), $table);
            if ($tableDiff)
                $this->schemaManager->alterTable($tableDiff);
        } else {
            $this->schemaManager->createTable($table);
        }
    }
}

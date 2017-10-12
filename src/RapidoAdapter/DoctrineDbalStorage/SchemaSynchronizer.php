<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
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
        $targetTable = new Table(
            $this->tableNameGenerator->generate($metadata->getName()),
            array_merge([$this->columnFactory->createColumn($metadata->getId()->getDataType(), $this->idColumnName)],
                array_map(function(ReadModelField $field) {
                    return $this->columnFactory->createColumn($field->getDataType(), $field->getId());
                }, $metadata->getFields())
            )
        );
        $targetTable->setPrimaryKey([$this->idColumnName]);

        $existingTables = array_filter(
            $this->schemaManager->listTables(),
            function(Table $existingTable) use ($targetTable) { return $targetTable->getName() == $existingTable->getName(); }
        );

        if (count($existingTables) > 1)
            throw new \UnexpectedValueException(sprintf("Multiple tables with the name '%s' exist.", $targetTable->getName()));

        if (count($existingTables) == 1) {
            $tableDiff = $this->schemaComparator->diffTable(current($existingTables), $targetTable);
            if ($tableDiff)
                $this->schemaManager->alterTable($tableDiff);
        } else {
            $this->schemaManager->createTable($targetTable);
        }
    }
}

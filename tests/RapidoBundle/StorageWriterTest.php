<?php

namespace Avoran\RapidoBundle;

use Avoran\Rapido\ReadModel\DataType\Boolean;
use Avoran\Rapido\ReadModel\DataType\Integer;
use Avoran\Rapido\ReadModel\DataType\TextString;
use Avoran\Rapido\ReadModel\ReadModelConfiguration;
use Avoran\Rapido\ReadModel\ReadModelField;
use Avoran\Rapido\ReadModel\ReadModelId;
use Avoran\Rapido\ReadModel\Record;
use Avoran\Rapido\ReadModel\StorageWriter;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StorageWriterTest extends KernelTestCase
{
    /** @var StorageWriter */
    private $writer;

    /** @var Connection */
    private $connection;

    public static function setUpBeforeClass()
    {
        unlink(__DIR__ . '/Config/sqlite.db');
    }

    protected function setUp()
    {
        $container = self::bootKernel()->getContainer();
        $this->writer = $container->get('rapido.storage_writer');
        $this->connection = $container->get('database_connection');
    }

    /** @test */
    public function it_should_create_a_table_on_first_insert()
    {
        $meta = new ReadModelConfiguration(
            'test',
            new ReadModelId(new Integer()),
            [
                new ReadModelField('f1', new Boolean()),
                new ReadModelField('f2', new TextString(10)),
            ],
            function ($data) { return new Record($data['id'], ['f1' => $data['f1'], 'f2' => $data['f2']]); },
            function () {}
        );

        $this->writer->writeRecord($meta, ['id' => 1, 'f1' => true, 'f2' => 'test']);

        $this->assertCount(1, $this->connection->createQueryBuilder()->select('*')->from('prefix_test')->execute()->fetchAll());
        $this->assertCount(3, $this->connection->createQueryBuilder()->select('*')->from('prefix_test')->execute()->fetch());
    }

    /** @test */
    public function it_should_update_the_table_on_record_change()
    {
        $meta = new ReadModelConfiguration(
            'test',
            new ReadModelId(new Integer()),
            [
                new ReadModelField('f1', new Boolean()),
                new ReadModelField('f2', new TextString(10)),
                new ReadModelField('f3', new TextString(10)),
            ],
            function ($data) { return new Record($data['id'], ['f1' => $data['f1'], 'f2' => $data['f2'], 'f3' => $data['f2']]); },
            function () {}
        );

        $this->writer->writeRecord($meta, ['id' => 2, 'f1' => true, 'f2' => 'test']);

        $this->assertEquals('test', $this->connection->createQueryBuilder()->select('f3')->from('prefix_test')->where('identifier = 2')->execute()->fetchColumn(0));
        $this->assertCount(4, $this->connection->createQueryBuilder()->select('*')->from('prefix_test')->execute()->fetch());
    }

    /** @test */
    public function it_should_update_a_record()
    {
        $meta = new ReadModelConfiguration(
            'test',
            new ReadModelId(new Integer()),
            [
                new ReadModelField('f1', new Boolean()),
                new ReadModelField('f2', new TextString(10))
            ],
            function ($data) { return new Record($data['id'], ['f1' => $data['f1'], 'f2' => $data['f2']]); },
            function () {}
        );

        $this->writer->writeRecord($meta, ['id' => 2, 'f1' => true, 'f2' => 'test2']);

        $this->assertEquals('test2', $this->connection->createQueryBuilder()->select('f2')->from('prefix_test')->where('identifier = 2')->execute()->fetchColumn(0));
        $this->assertCount(3, $this->connection->createQueryBuilder()->select('*')->from('prefix_test')->execute()->fetch());
    }

    /** @test */
    public function it_should_create_a_snapshot_table()
    {
        $meta = new ReadModelConfiguration(
            'test',
            new ReadModelId(new Integer()),
            [
                new ReadModelField('f1', new Boolean()),
                new ReadModelField('f2', new TextString(10)),
            ],
            function ($data) { return new Record($data['id'], ['f1' => $data['f1'], 'f2' => $data['f2']]); },
            function () {},
            'created_at'
        );

        $this->writer->writeRecord($meta, ['id' => 1, 'f1' => true, 'f2' => 'test']);

        $this->assertEquals(1, $this->connection->fetchColumn("SELECT count(*) FROM sqlite_master WHERE type='table' AND name='prefix_test_suffix'"));
    }
}

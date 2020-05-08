<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Avoran\Rapido\ReadModel\DataType\Boolean;
use Avoran\Rapido\ReadModel\DataType\DateTime;
use Avoran\Rapido\ReadModel\DataType\Decimal;
use Avoran\Rapido\ReadModel\DataType\Integer;
use Avoran\Rapido\ReadModel\DataType\TextString;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;

class DbalTypeMapperTest extends TestCase
{
    /** @test */
    public function it_should_give_the_correct_options()
    {
        $mapper = new DbalTypeMapper();

        $this->assertEquals(Type::getType(Types::INTEGER), $mapper->mapReadModelToDbalType(new Integer()));
        $this->assertEquals(Type::getType(Types::DECIMAL), $mapper->mapReadModelToDbalType(new Decimal(10, 7)));
        $this->assertEquals(Type::getType(Types::BOOLEAN), $mapper->mapReadModelToDbalType(new Boolean()));
        $this->assertEquals(Type::getType(Types::DATETIME_MUTABLE), $mapper->mapReadModelToDbalType(new DateTime()));
        $this->assertEquals(Type::getType(Types::STRING), $mapper->mapReadModelToDbalType(new TextString(255)));
        $this->assertEquals(Type::getType(Types::TEXT), $mapper->mapReadModelToDbalType(new TextString()));
    }
}

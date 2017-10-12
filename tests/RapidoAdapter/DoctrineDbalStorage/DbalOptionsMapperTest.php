<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

use Avoran\Rapido\ReadModel\DataType\Boolean;
use Avoran\Rapido\ReadModel\DataType\DateTime;
use Avoran\Rapido\ReadModel\DataType\Decimal;
use Avoran\Rapido\ReadModel\DataType\Integer;
use Avoran\Rapido\ReadModel\DataType\TextString;
use PHPUnit\Framework\TestCase;

class DbalOptionsMapperTest extends TestCase
{
    /** @test */
    public function it_should_give_the_correct_options()
    {
        $mapper = new DbalOptionsMapper();

        $defaultOptionFields = [
            new Boolean(),
            new DateTime(),
            new Integer(),
            new TextString()
        ];

        foreach ($defaultOptionFields as $field) {
            $this->assertEquals(['notnull' => false], $mapper->mapReadModelToDbalOptions($field));
        }

        $expected = ['notnull' => false, 'scale' => 10, 'precision' => 7];
        $actual = $mapper->mapReadModelToDbalOptions(new Decimal(10, 7));
        $this->assertEquals(sort($expected), sort($actual));
    }
}

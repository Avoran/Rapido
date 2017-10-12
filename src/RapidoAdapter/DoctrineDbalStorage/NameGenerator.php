<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

class NameGenerator
{
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function generate($id)
    {
        return $this->prefix . (string) $id;
    }
}

<?php

namespace Avoran\RapidoAdapter\DoctrineDbalStorage;

class NameGenerator
{
    private $prefix;
    private $suffix;

    public function __construct($prefix, $suffix)
    {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function generate($id)
    {
        return $this->prefix . (string) $id;
    }

    public function generateWithSuffix($id)
    {
        return $this->generate($id) . $this->suffix;
    }
}

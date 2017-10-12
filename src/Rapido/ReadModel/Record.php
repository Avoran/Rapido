<?php

namespace Avoran\Rapido\ReadModel;

final class Record
{
    private $id;
    public function getId() { return $this->id; }

    private $data;
    public function getData() { return $this->data; }

    public function __construct($id, $data)
    {
        $this->id = $id;
        $this->data = $data;
    }
}

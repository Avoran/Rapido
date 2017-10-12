<?php

namespace Avoran\Rapido\ReadModel;

interface StorageWriter
{
    public function writeRecord(ReadModelConfiguration $metadata, $recordData);
}

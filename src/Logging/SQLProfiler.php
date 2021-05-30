<?php

namespace PoK\Logging;

use PoK\SQLQueryBuilder\Profiler\RecordQueryInterface;

class SQLProfiler implements RecordQueryInterface
{
    private $queries = [];
    
    public function recordQuery(string $query)
    {
        $this->queries[] = $query;
    }

//    public function dumpRecord()
//    {
//    }
}

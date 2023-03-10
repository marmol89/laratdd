<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

trait DetectRepeatedQueries
{
    function enableQueryLog()
    {
        DB::enableQueryLog();
    }

    function assertNotRepeatedQueries()
    {
        $queries = array_column(DB::getQueryLog(), 'query');

        $selects = array_filter($queries, function ($query) {
            return strpos($query, 'select') === 0;
        });

        $selects = array_count_values($selects);

        foreach ($selects as $select => $amount) {
            $this->assertEquals(
                1,
                $amount,
                "The following SELECT was executed $amount times: \n\n $select"
            );
        }
    }

    function flushQueryLog()
    {
        DB::flushQueryLog();
    }
}

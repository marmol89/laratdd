<?php

namespace Tests;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;

class TestCollectionData
{
    private $collection;

    function __construct($collection)
    {
        if (! $collection instanceof Collection &&
            ! $collection instanceof AbstractPaginator) {
            PHPUnit::fail('The data is not a collection');
        }

        $this->collection = $collection;
    }

    function contains($data)
    {
        PHPUnit::assertTrue($this->collection->contains($data));

        return $this;
    }

    function notContains($data)
    {
        PHPUnit::assertFalse($this->collection->contains($data));

        return $this;
    }
}

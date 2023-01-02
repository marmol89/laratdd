<?php

namespace App;

use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorAlias;

class LengthAwarePaginator extends LengthAwarePaginatorAlias
{
    public function parameters()
    {
        return $this->query;
    }
}

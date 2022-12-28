<?php

namespace App;

use Illuminate\Database\Eloquent\Builder as Builder;
use Illuminate\Support\Facades\DB;

class QueryBuilder extends Builder
{
    public function whereQuery($subquery , $operator , $value = null){
        $this->addBinding($subquery->getBindings());
        $this->where(DB::raw("({$subquery->toSql()})") , $operator ,  $value);
        return $this;
    }

    public function onlyTrashedIf($value)
    {
        if($value){
            $this->onlyTrashed();
        }
        return $this;
    }
}
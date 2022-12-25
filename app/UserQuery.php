<?php

namespace App;


class UserQuery extends QueryBuilder
{
    public function findByEmail($email)
    {
        return static::whereEmail($email)->first();
    }

}
<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserFilter extends QueryFilter
{

    public function rules(): array
    {
        return [
            'search' => 'filled',
            'state' => 'in:active,inactive',
            'role' => 'in:admin,user',
            'skills' => 'array|exists:skills,id',
            'from' => 'date_format:d/m/Y',
            'to' => 'date_format:d/m/Y',
            'order' => 'in:first_name,email,created_at',
            'direction' => 'in:asc,desc',
        ];
    }

    public function search($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('team', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
        });
    }

    public function state($query, $state)
    {
        return $query->where('active', $state === 'active');
    }

    public function skills($query , $skills)
    {

        $subquery = DB::table('skill_user as s')
            ->selectRaw('COUNT(s.id)')
            ->whereColumn('s.user_id' , 'users.id')
            ->whereIn('skill_id' , $skills);

       return $query->whereQuery($subquery , count($skills));

    }

    public function from($query , $date)
    {
        $date = Carbon::createFromFormat('d/m/Y' , $date);

       return $query->whereDate('created_at' , '>=' , $date);
    }

    public function to($query , $date)
    {
        $date = Carbon::createFromFormat('d/m/Y' , $date);
        return $query->whereDate('created_at' , '<=' , $date);
    }

    public function order($query , $value)
    {
        return $query->orderBy($value , $this->valid['direction'] ?? 'asc');
    }

    public function direction($query , $value)
    {

    }

}
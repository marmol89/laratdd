<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserFilter extends QueryFilter
{
    protected $aliasses = [
        'date' => 'created_at',
    ];

    public function getColumnName($alias){
        return $this->aliasses[$alias] ?? $alias;
    }

    public function rules(): array
    {
        return [
            'search' => 'filled',
            'state' => 'in:active,inactive',
            'role' => 'in:admin,user',
            'skills' => 'array|exists:skills,id',
            'from' => 'date_format:d/m/Y',
            'to' => 'date_format:d/m/Y',
            'order' => 'in:first_name,email,date,first_name-desc,email-desc,date-desc',
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
        if(Str::endsWith($value, '-desc')){
            return $query->orderByDesc($this->getColumnName(Str::substr($value,0,-5)));
        }else {
            return $query->orderBy($this->getColumnName($value));
        }
    }
}
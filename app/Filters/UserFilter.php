<?php

namespace App\Filters;

use App\Login;
use App\QueryFilter;
use App\Rules\SortableColumn;
use App\Sortable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserFilter extends QueryFilter
{
    protected $aliasses = [
        'date' => 'created_at',
        'login' => 'last_login_at'
    ];

    public function getColumnName($alias)
    {
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
            'order' => [new SortableColumn(['first_name' , 'email' , 'date' , 'login'])],
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

    public function skills($query, $skills)
    {
        $subquery = DB::table('skill_user as s')
            ->selectRaw('COUNT(s.id)')
            ->whereColumn('s.user_id', 'users.id')
            ->whereIn('skill_id', $skills);

        return $query->whereQuery($subquery, count($skills));
    }

    public function from($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        return $query->whereDate('created_at', '>=', $date);
    }

    public function to($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);
        return $query->whereDate('created_at', '<=', $date);
    }

    public function order($query, $value)
    {
        [$column , $direction] = Sortable::info($value);
        return $query->orderBy($this->getColumnName($column), $direction);
    }
}

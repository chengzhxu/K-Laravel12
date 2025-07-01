<?php

namespace App\Services\System;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RoleService extends BaseService
{
    public function handleSearch(Builder $query, Request $request): Builder
    {
        $name = $request->input('name');
        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($request->has('date_type')) {
            $date_type = $request->input('date_type');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            switch ($date_type) {
                case 'createTime':
                    $query->whereBetween('created_at', [$start_time, $end_time]);
                    break;
                case 'updateTime':
                    $query->whereBetween('updated_at', [$start_time, $end_time]);
                    break;
            }
        }
        return $query;
    }
}

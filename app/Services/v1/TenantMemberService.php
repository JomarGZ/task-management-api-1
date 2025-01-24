<?php
namespace App\Services\v1;

class TenantMemberService {

    public function getValidSortDirection($direction)
    {
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc'; 
        }
        return $direction;
    }
    public function getValidSortColumn($column)
    {
        $validColumns = ['name', 'email', 'created_at'];
        if (!in_array($column, $validColumns)) {
            $column = 'created_at';
        }
        
        return $column;
    }
}
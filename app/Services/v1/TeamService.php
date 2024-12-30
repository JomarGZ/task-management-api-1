<?php
namespace App\Services\V1;

class TeamService {

    public function getValidSortDirection($direction)
    {
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc'; 
        }
        return $direction;
    }
    public function getValidSortColumn($column)
    {
        $validColumns = ['name'];
        if (!in_array($column, $validColumns)) {
            $column = 'created_at';
        }
        
        return $column;
    }
}
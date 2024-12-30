<?php
namespace App\Services\V1;

class ProjectService {

    public function getValidSortDirection($direction)
    {
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc'; 
        }
        return $direction;
    }
    public function getValidSortColumn($column)
    {
        $validColumns = ['name', 'description', 'created_at'];
        if (!in_array($column, $validColumns)) {
            $column = 'created_at';
        }
        
        return $column;
    }
}
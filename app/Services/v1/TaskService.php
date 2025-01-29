<?php
namespace App\Services\v1;

class TaskService {

    public function getValidSortDirection($direction)
    {
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc'; 
        }
        return $direction;
    }
    public function getValidSortColumn($column)
    {
        $validColumns = [
            'title', 
            'description',
            'priority_levels',
            'status',
            'deadline_at',
            'started_at',
            'completed_at',
            'deadline_at'
        ];
        if (!in_array($column, $validColumns)) {
            $column = 'created_at';
        }
        
        return $column;
    }
}
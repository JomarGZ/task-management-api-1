<?php
namespace App\Services\v1;

class TaskStatisticService {
    public function setTimeFrame($timeFrame, $range = 1, $end = null) 
    {
        $endDate = $end ?? now();
        $startDate = clone $endDate;

        $timeFrame = match($timeFrame) {
            'weekly' =>  [
                'start_date' => $startDate->copy()->subWeeks($range)->startOfWeek(),
                'end_date' => $startDate->copy()->subWeeks($range)->endOfWeek(),
            ],
            'monthly' => [
                'start_date' => $startDate->copy()->subMonths($range)->startOfMonth(),
                'end_date' => $startDate->copy()->subMonths($range)->endOfMonth(),
            ],
            default => [
                'start_date' => $startDate->copy()->subDays($range)->startOfDay(),
                'end_date' => $startDate->copy()->subDays($range)->endOfDay()
            ]
        };

        return $timeFrame;
    }
}
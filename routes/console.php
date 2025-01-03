<?php

use App\Console\Commands\NotifyUpcomingTaskDeadlines;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command(NotifyUpcomingTaskDeadlines::class)->dailyAt('00:00');

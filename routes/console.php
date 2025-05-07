<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::command('schedule:run', function () {
//     $schedule = app(Schedule::class);
//     $schedule->command('app:check-daily-presence')->everyMinute();
// });

app()->booted(function () {
    $schedule = app(Schedule::class);
    $schedule->command('app:check-daily-presence')->everyMinute();
});
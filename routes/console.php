<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

$scheduleTimezone = (string) config('app.schedule_timezone', config('app.timezone'));

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('report:weekly-site')
    ->sundays()
    ->at('20:00')
    ->timezone($scheduleTimezone);

Schedule::command('content:publish-scheduled')
    ->everyMinute()
    ->timezone($scheduleTimezone);

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('report:weekly-site')
    ->sundays()
    ->at('20:00')
    ->timezone('Africa/Lagos');

Schedule::command('content:publish-scheduled')
    ->everyMinute()
    ->timezone('Africa/Lagos');

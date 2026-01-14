<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule article publishing every minute for immediate scheduling
Schedule::command('articles:publish-scheduled')->everyMinute();

// Expire unpaid orders hourly
Schedule::command('orders:expire-unpaid')->hourly();

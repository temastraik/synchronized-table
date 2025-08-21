<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\FetchGoogleSheetComments::class,
        \App\Console\Commands\SyncGoogleSheets::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('google-sheet:sync')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
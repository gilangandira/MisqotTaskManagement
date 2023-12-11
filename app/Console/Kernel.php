<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('message:notification')->daily('09:00')->appendOutputTo('notifications.log');
        $schedule->command('checker:statustask')->daily('01:00')->appendOutputTo('status.log');
        $schedule->command('command:performance')->monthlyOn(26, '20:00')->appendOutputTo('performance.log');
    }

    protected function scheduleTimezone()
    {
        return 'Asia/Jakarta';
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }


}

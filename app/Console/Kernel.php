<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      \App\Console\Commands\MySqlDumpCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      $DB_DUMP_TIME = env('DB_DUMP_TIME');
      $APP_ROOT = env('APP_ROOT');
      echo 'LAMOID SCHEDULER: ' . date('Y-m-d H:i:s') . PHP_EOL;
      $schedule->exec("php {$APP_ROOT}/artisan db:dump")->dailyAt($DB_DUMP_TIME);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

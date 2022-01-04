<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Illuminate\Support\Facades\Artisan;

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
      $url = 'http://lamoid.nginx/api/v1/db-backup/send-to-drive?code=' . env('GOOGLE_DRIVE_UPLOAD_RUN_CODE');
      $schedule
        ->exec("php {$APP_ROOT}/artisan db:dump")
        ->dailyAt($DB_DUMP_TIME)
        ->thenPing($url);
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

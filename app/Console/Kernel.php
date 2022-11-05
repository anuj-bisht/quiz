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
        'App\Console\Commands\MakeDefaultTip',
	'App\Console\Commands\sendNotification',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('maketip:default')->everyMinute();
	$schedule->command('plannotification:default')->everyMinute();
         $schedule->call(function () {
            // echo "coming h rerre";
            // $myfile = fopen("/var/www/html/quizs/public/uploads/f_".rand(100,1000).".txt", "w") or die("Unable to open file!");
            // $txt = "John Doe\n";
            // fwrite($myfile, $txt);
            // $txt = "Jane Doe\n";
            // fwrite($myfile, $txt);
            // fclose($myfile);
         })->everyMinute();
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

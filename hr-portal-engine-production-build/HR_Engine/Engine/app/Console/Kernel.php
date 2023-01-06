<?php

namespace App\Console;

use App\Console\Commands\LoadOagFlightData;
use App\Console\Commands\LoadMetroData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \Spatie\ShortSchedule\ShortSchedule;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    //   $schedule->command(LoadOagFlightData::class)->everyMinute()->withoutOverlapping();
        //  $schedule->command(LoadMetroData::class)->everyMinute();
         $schedule->command('sanctum:prune-expired --hours=24')->daily()->withoutOverlapping();
                 
    }

    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
      // this artisan command will run every 30 seconds
    //   $shortSchedule->command(LoadMetroData::class)->everySeconds(30)->withoutOverlapping();
    
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

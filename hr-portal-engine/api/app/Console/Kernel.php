<?php

namespace App\Console;

use App\Console\Commands\LoadOagFlightData;
use App\Console\Commands\LoadMetroData;
use App\Console\Commands\AutoIncrementLeaves;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \Spatie\ShortSchedule\ShortSchedule;
use Illuminate\Support\Facades\Log;

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
        $schedule->command(AutoIncrementLeaves::class)->yearly();
        // $schedule->command(AutoIncrementLeaves::class)->lastDayOfMonth();
    }

    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
      // this artisan command will run every 30 seconds
    //   $shortSchedule->command(AutoIncrementLeaves::class)->everySeconds(10)->withoutOverlapping();
    
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

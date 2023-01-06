<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TaskHandlerLoadOagFlightData extends Command
{
    private const UPL = 'up';
    private const UP = 'u';
    private const DOWNL = 'down';
    private const DOWN = 'd';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:oag:load {--' . self::UPL . '} {--' . self::DOWNL . '} {--' . self::UP . '} {--' . self::DOWN . '}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start or stop the load oag flight data scheduled task.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      if ($this->option(self::UP) || $this->option(self::UPL)) {
        Storage::put(LoadOagFlightData::$KEY, 'enabled');
      } else if ($this->option(self::DOWN) || $this->option(self::DOWNL)) {
        Storage::put(LoadOagFlightData::$KEY, 'disabled');
      }
    }
}


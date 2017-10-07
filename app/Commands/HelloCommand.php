<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class HelloCommand extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'hello';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Welcome to LDK helper.';

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Love docker and Laradock? We do too.');
        $this->comment('Want to see more? Run: ldk list');
    }

    /**
     * Define the command's schedule.
     *
     * Add the following cron entry:
     *     * * * * * php /path-to-your-project/your-app-name schedule:run >> /dev/null 2>&1
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}

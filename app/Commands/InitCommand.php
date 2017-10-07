<?php

namespace App\Commands;

use App\CliHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
    use CliHelper;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install the laradock project on your current folder.';

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Cloning the laradock folder, this might take some time...');
        $basePath = getcwd();

        if (! File::exists($basePath.'/.ldk/')) {
            $this->runQuietly('git clone git@github.com:laradock/laradock.git .ldk', function ($process, $output) {
                $this->error('Something went wrong.');
                exit;
            });

            $this->info('Configuring the your global setup...');
            $this->info('Done!');
            $this->notify('Finished setup', 'LDK successfully installed!');
        } else {
            $this->info('The LDK was already configured.');
        }
    }
}

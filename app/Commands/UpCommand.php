<?php

namespace App\Commands;

use App\CliHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class UpCommand extends Command
{
    use CliHelper;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'up {services?*}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Starts the base containers if they are not already started.';

    private $defaultServices = ['nginx', 'mysql'];

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Starting the desired services...');
        $pwd = getcwd();
        $ldkPath = dirname($pwd) . DIRECTORY_SEPARATOR . '.ldk' . DIRECTORY_SEPARATOR;
        $projectFolderBaseName = basename($pwd);

        if (! File::exists($ldkPath)) {
            $this->error('You have not configured Laradock yet.');
        } else {
            $services = (array) $this->argument('services') ?: $this->defaultServices;

            $this->runThru(trim("cd {$ldkPath} && docker-compose up -d ". implode(' ', $services)));

            $this->info('Done!');
        }
    }
}

<?php

namespace App\Commands;

use App\CliHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ArtisanCommand extends Command
{
    use CliHelper;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'artisan {commands?*}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Runs artisan for your current project.';

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $pwd = getcwd();
        $ldkPath = dirname($pwd) . DIRECTORY_SEPARATOR . '.ldk' . DIRECTORY_SEPARATOR;
        $projectFolderBaseName = basename($pwd);

        if (! File::exists($ldkPath)) {
            $this->error('You have not configured Laradock yet.');
        } else {
            $commands = (array) $this->argument('commands');
            $currentUserId = exec('id -u');

            $this->runThru(trim("cd {$ldkPath} && docker-compose exec --user {$currentUserId} workspace php {$projectFolderBaseName}/artisan " . implode(' ', $commands)));
        }
    }
}

<?php

namespace App\Commands;

use App\CliHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class LogsCommand extends Command
{
    use CliHelper;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'logs {args?*}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Watches the logs.';

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
            $args = (array) $this->argument('args') ?: ['-f'];
            $this->runThru(trim("cd {$ldkPath} && docker-compose logs ". implode(' ', $args)));
        }
    }
}

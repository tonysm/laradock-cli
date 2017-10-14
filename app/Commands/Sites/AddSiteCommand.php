<?php

namespace App\Commands\Sites;

use Spyc;
use App\Sites;
use App\CliHelper;
use App\Yaml\SpycInterpreter;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AddsiteCommand extends Command
{
    use CliHelper;

    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:add {host?} {docroot?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Registers a site and reboots nginx container.';

    /**
     * Execute the command. Here goes the code.
     *
     * @return void
     */
    public function handle(): void
    {
        $host = $this->argument('host') ?: $projectFolderBaseName . '.ldk';
        $root = $this->argument('docroot') ?: 'public';
        $this->info("Adding new site http://{$host}/ with document root: {$root}/");
        
        $pwd = getcwd();
        $ldkPath = dirname($pwd) . DIRECTORY_SEPARATOR . '.ldk' . DIRECTORY_SEPARATOR;
        $projectFolderBaseName = basename($pwd);

        if (! File::exists($ldkPath)) {
            $this->error('You have not configured Laradock yet.');
        } else {
            (new Sites($ldkPath, new SpycInterpreter(new Spyc())))
                ->addVirtualHost($projectFolderBaseName, $host, $root)
                ->registerContainerAliases($host)
                ->restartRunningServicesAndNetworks();
        }
    }
}

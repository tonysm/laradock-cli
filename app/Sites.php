<?php

namespace App;

use App\Yaml\Interpreter as YamlInterpreter;

class Sites
{
    use CliHelper;

    /**
     * The full path to the LDK setup.
     *
     * @var string
     */
    private $ldkPath;

    /**
     * @var \App\Yaml\Interpreter
     */
    private $yaml;

    /**
     * @param string $ldkPath
     * @param \App\Yaml\Interpreter $yaml
     */
    public function __construct($ldkPath, YamlInterpreter $yaml)
    {
        $this->ldkPath = $ldkPath;
        $this->yaml = $yaml;
    }

    /**
     * Creates a new VirtualHost in the nginx container poiting.
     * Registering the given hostname and document inside the
     * project path inside the containers.
     *
     * @param string $projectFolderBaseName
     * @param string $hostName
     * @param string $docRoot
     * @return $this
     */
    public function addVirtualHost($projectFolderBaseName, $hostName, $docRoot)
    {
        $vhostConfig = file_get_contents($this->ldkPath . '/nginx/sites/laravel.conf.example');
        $vhostConfig = str_replace('laravel.dev', $hostName, $vhostConfig);
        $vhostConfig = str_replace('/laravel/public', "/{$projectFolderBaseName}/{$docRoot}", $vhostConfig);
        
        file_put_contents("{$this->ldkPath}/nginx/sites/{$projectFolderBaseName}.conf", $vhostConfig);

        return $this;
    }

    /**
     * Restarts all running services so the networking takes effect.
     *
     * @return $this
     */
    public function restartRunningServicesAndNetworks()
    {
        $runningServices = array_diff($this->detectRunningServices(), ['applications', 'workspace']);

        $this->runThru("cd {$this->ldkPath} && docker-compose down");
        $this->runThru("cd {$this->ldkPath} && docker-compose up -d ". implode(' ', $runningServices));

        return $this;
    }

    /**
     * Returns the service names of all running containers.
     *
     * @return string[]
     */
    private function detectRunningServices()
    {
        $output = $this->runCommand("cd {$this->ldkPath} && docker-compose ps");

        // Docker compose returns a table of the running container names
        // but we only need the first item of that table.
        $runningContainerNames = array_map(function ($line)  {
            preg_match('#^(\w*)\s*.*$#', $line, $matches);
            return $matches[1];
        }, array_slice(array_filter(explode(PHP_EOL, $output)), 2));

        // With the container names, we can extract only the service name from it
        // because docker-compose sets a pattern of basefolder_servicename_1.
        return array_values(array_unique(array_filter(array_map(function ($containerName) {
            preg_match('#^ldk_(.*)_\d*$#', $containerName, $matches);
            return $matches[1] ?? null;
        }, $runningContainerNames))));
    }

    /**
     * Register the hostname as an alias to the Nginx container
     * in all networks it's in, so we can have inter-container
     * communication using the hostnames.
     *
     * @param string $hostName
     * @return $this
     */
    public function registerContainerAliases($hostName)
    {
        $dockerComposeFile = $this->yaml->fromFile("{$this->ldkPath}/docker-compose.yml");

        $networks = $dockerComposeFile['services']['nginx']['networks'];
        $newNetworks = [];

        foreach ($networks as $key => $network) {
            if (is_string($key) && isset($network['aliases'])) {
                $newNetworks[$key]['aliases'] = array_values(array_unique(array_merge($network['aliases'], [$hostName])));
            } else {
                $newNetworks[$network] = ['aliases' => [$hostName]];
            }
        }

        $dockerComposeFile['services']['nginx']['networks'] = $newNetworks;

        file_put_contents("{$this->ldkPath}/docker-compose.yml", $this->yaml->toYaml($dockerComposeFile));

        return $this;
    }
}
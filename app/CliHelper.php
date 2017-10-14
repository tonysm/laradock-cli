<?php

namespace App;

use Symfony\Component\Process\Process;

trait CliHelper
{
    protected function runQuietly($command, $onError = null)
    {
        $onError = $onError ?: function () {};

        ($process = new Process($command.' > /dev/null 2>&1'))
            ->setTimeout(null)
            ->run();

        if ($process->getExitCode() > 0) {
            return $onError($process);
        }
    }

    protected function runCommand($command, $onError = null)
    {
        $onError = $onError ?: function () {};
            $process = new Process($command);
            $output = '';
            $process->setTimeout(null)
                ->run(function ($type, $line) use (&$output) {
                    $output .= $line;
                });
    
            if ($process->getExitCode() > 0) {
                $onError($process, $output);
            }
            
            return $output;
    }

    protected function runThru($command)
    {
        passthru($command);
    }
}
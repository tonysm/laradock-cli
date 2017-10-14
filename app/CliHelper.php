<?php

namespace App;

trait CliHelper
{
    protected function runQuietly($command, $onError = null)
    {
        $onError = $onError ?: function () {};
        $process = new \Symfony\Component\Process\Process($command.' > /dev/null 2>&1');
        $output = '';
        $process->setTimeout(null)
            ->run(function ($type, $line) use (&$output) {
                $output .= $line;
            });

        if ($process->getExitCode() > 0) {
            $onError($process, $output);
        }
    }

    protected function runThru($command)
    {
        passthru($command);
    }
}
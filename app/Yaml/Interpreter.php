<?php

namespace App\Yaml;

interface Interpreter
{
    /**
     * Loads the given Yaml file as array.
     *
     * @param string $file
     * @return array
     */
    public function fromFile($file) : array;

    /**
     * Parses the given data to Yaml.
     *
     * @param array $data
     * @return string
     */
    public function toYaml(array $data) : string;
}
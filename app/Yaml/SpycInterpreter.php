<?php

namespace App\Yaml;

use Spyc;

class SpycInterpreter implements Interpreter
{
    /**
     * @var \Spyc
     */
    private $yamlLoader;

    /**
     * @param Spyc $yamlLoader
     */
    public function __construct(Spyc $yamlLoader)
    {
        $this->yamlLoader = $yamlLoader;
    }

    /**
     * Loads the given Yaml file as array.
     *
     * @param string $file
     * @return array
     */
    public function fromFile($file) : array
    {
        return $this->yamlLoader->loadFile($file);
    }
    
    /**
     * Parses the given data to Yaml.
     *
     * @param array $data
     * @return string
     */
    public function toYaml(array $data) : string
    {
        return $this->yamlLoader->dump($data);
    }
}
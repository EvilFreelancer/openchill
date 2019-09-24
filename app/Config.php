<?php

namespace OpenChill;

class Config
{
    /**
     * @var array
     */
    public $parameters = [
        'namespace' => 'OpenChillClient',
        'author'    => 'OpenChill PHP Codegen',
        'type'      => 'library',
        'output'    => __DIR__ . '/../output/',
        'version'   => '0.0.1',
    ];

    /**
     * List of allowed parameters
     *
     * @var array
     */
    public $allowed = [
        'name',
        'type',
        'license',
        'author',
        'version',
        'namespace',
        'input',
        'output',
    ];

    /**
     * Default structure of output project
     *
     * @var array
     */
    public $structure = [
        'f|composer.json',
        'f|phpunit.xml',
        'd|src' => [
            'd|Models',
            'd|Endpoints',
            'f|Client.php',
            'f|Config.php',
        ],
        'd|tests',
    ];

    /**
     * Convert full class name to path on filesystem
     *
     * @param string $class
     * @return string
     */
    public function classToPath(string $class): string
    {
        return $this->output . '/' . preg_replace('/\\\\/', '/', $class) . '.php';
    }

    /**
     * Extract directory from path of file
     *
     * @param string $class
     * @return string
     */
    public function classToFolder(string $class): string
    {
        return dirname($this->classToPath($class));
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set(string $name, string $value)
    {
        if (!\in_array($name, $this->allowed, true)) {
            throw new \InvalidArgumentException('Argument is not allowed [' . implode(',', $this->allowed . ']'));
        }

        $this->parameters[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function __get(string $name): string
    {
        if (!isset($this->parameters[$name])) {
            throw new \InvalidArgumentException('Argument is not set');
        }

        return $this->parameters[$name];
    }
}

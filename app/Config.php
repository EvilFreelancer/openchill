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
        'output'    => __DIR__ . '/../client/',
    ];

    /**
     * @var array
     */
    public $allowed = [
        'name',
        'type',
        'license',
        'author',
        'namespace',
        'input',
        'output'
    ];

    /**
     * @var array
     */
    public $structure = [
        'f|composer.json',
        'd|src' => [
            'd|Models',
            'd|Endpoints',
            'f|Client.php',
            'f|Config.php',
        ],
        'd|tests',
    ];

    /**
     * @param string $class
     * @return string
     */
    public function classToPath(string $class): string
    {
        return $this->output . '/' . preg_replace('/\\\\/', '/', $class) . '.php';
    }

    /**
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

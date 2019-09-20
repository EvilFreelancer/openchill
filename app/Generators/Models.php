<?php

namespace OpenChill\Generators;

use gossi\codegen\generator\builder\FunctionBuilder;
use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpProperty;
use OpenChill\Config;

class Models
{
    /**
     * @var array
     */
    protected $specifications = [];
    protected $models         = [];
    protected $config;

    /**
     * @param Config $config
     * @param array  $specifications
     * @return $this
     */
    public static function factory(Config $config, array $specifications): self
    {
        $object = new self($config, $specifications);
        $object->generate();
        return $object;
    }

    /**
     * Models constructor.
     *
     * @param Config $config
     * @param array  $specifications
     */
    public function __construct(Config $config, array $specifications)
    {
        $this->config         = $config;
        $this->specifications = $specifications;
        $this->models         = $specifications['definitions'];
    }

    /**
     * Generate models by specification and return count of generated models
     *
     * @return int
     */
    public function generate(): int
    {
        $totalModels = 0;

        // Parse models from specification
        foreach ($this->models as $modelName => $modelParameters) {

            // Namespace of class
            $namespace = $this->config->namespace . '\\Models';
            $className = $namespace . '\\' . $modelName;

            // Initiate class abstraction
            $class = new PhpClass();
            $class->setQualifiedName($className);

            // Get properties of model
            $properties = [];
            if (!empty($modelParameters['properties'])) {
                $properties = $modelParameters['properties'];
            }

            // Parse model properties
            foreach ($properties as $propertyName => $propertyParameters) {

                // Custom property wrapper
                $property = new Property($propertyName, $propertyParameters);
                $property = $property->generate();

                // Set use statements in Models namespace
                foreach ($property->getUses() as $use) {
                    $class->addUseStatement($namespace . '\\' . $use);
                }

                // Add property to class
                $class->setProperty($property->getProperty());
            }

            // Generate code
            $generator = new CodeFileGenerator();
            $code      = $generator->generate($class);

            // Create folder by class namespace if not exist
            if (!file_exists($this->config->classToFolder($className))) {
                mkdir($this->config->classToFolder($className), 0755, true);
            }

            // Save file by path
            file_put_contents($this->config->classToPath($className), $code);

            $totalModels++;
        }

        return $totalModels;
    }

}

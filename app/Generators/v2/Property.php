<?php

namespace OpenChill\Generators\v2;

use gossi\codegen\model\PhpProperty;

class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var array
     */
    private $description = [];

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $uses = [];

    /**
     * @var PhpProperty
     */
    private $property;

    /**
     * Property constructor.
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct(string $name, array $parameters)
    {
        $this->name       = $name;
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return PhpProperty
     */
    public static function factory(string $name, array $parameters): PhpProperty
    {
        $object = new self($name, $parameters);
        return $object->generate();
    }

    /**
     * Convert @ref line to model name
     * eg. "$ref": "#/definitions/MatchingSlotsSearchParameters" to "MatchingSlotsSearchParameters"
     *
     * @param string $ref
     * @return string
     */
    private function refToClass(string $ref): string
    {
        $items = explode('/', $ref);
        return $items[array_key_last($items)];
    }

    /**
     * Extract type of property
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        if (!empty($this->parameters['type'])) {
            $this->type = $this->parameters['type'];
        }

        if (!empty($this->parameters['items'])) {
            $this->type = 'array';
        }

        if (!empty($this->parameters['$ref'])) {

            // Get name of model
            $ref = $this->refToClass($this->parameters['$ref']);

            // Save to uses
            $this->uses[] = $ref;

            // Return type is array of objects
            $this->type = $ref . '[]';
        }

        return $this->type ?? null;
    }

    /**
     * Extract description
     *
     * @return array|null
     */
    public function getDescription(): ?array
    {
        if (!empty($this->parameters['title'])) {
            $this->description[] = trim($this->parameters['title']);
            $this->description[] = '';
        }

        if (!empty($this->parameters['description'])) {
            $this->description[] = trim($this->parameters['description']);
            $this->description[] = '';
        }

        if (!empty($this->parameters['enum'])) {
            $this->description[] = 'Enum [' . implode(',', $this->parameters['enum']) . ']';
        }

        if (!empty($this->parameters['readOnly'])) {
            $this->description[] = 'This parameter is read only.';
        }


        return $this->description ?? null;
    }

    /**
     * @return PhpProperty
     */
    public function getProperty(): PhpProperty
    {
        return $this->property;
    }

    /**
     * @return $this
     */
    public function generate(): self
    {
        // Init property
        $property = PhpProperty::create($this->name);
        $property->setVisibility('public');

        // Set type of property
        if (null !== $this->getType()) {
            $property->setType($this->type);
        }

        // Set description of property
        if (null !== $this->getDescription()) {
            $property->setDescription($this->description);
        }

        // Set property
        $this->property = $property;

        return $this;
    }
}

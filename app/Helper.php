<?php

namespace OpenChill;

trait Helper
{
    /**
     * @var bool
     */
    protected $isYaml = false;

    /**
     * @var bool
     */
    protected $isJson = false;

    /**
     * @var array
     */
    protected $content = [];

    /**
     * Check if file is YAML
     *
     * @param string $source
     * @return bool
     */
    public function isYaml(string &$source): bool
    {
        $yaml = yaml_parse($source);

        if ($yaml !== false) {
            $this->isYaml  = true;
            $this->content = $yaml;
        }

        return $yaml !== false;
    }

    /**
     * Check if file is YAML
     *
     * @param string $source
     * @return bool
     */
    public function isJson(string &$source): bool
    {
        $json = json_encode($source);

        if ($json !== false) {
            $this->isJson  = true;
            $this->content = $json;
        }

        return $json !== false;
    }
}

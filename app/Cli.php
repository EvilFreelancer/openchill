<?php

namespace OpenChill;

use ComposerJson\Schemas\Composer;
use OpenChill\Generators\ComposerJson;
use OpenChill\Generators\v2\Models;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Cli
{
    /**
     * @var Config
     */
    private $config;

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
     * Cli constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Check if file is YAML
     *
     * @param string $source
     *
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
     *
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

    /**
     * @throws \Exception
     */
    public function run()
    {
        (new Application('OpenChill OpenAPI/Swagger code generator'))
            ->register('parse')
            ->setDescription('Parse Swagger/OpenAPI config to PHP client')
            ->addOption('namespace', 'ns', InputArgument::OPTIONAL, 'Name of root namespace', $this->config->namespace)
            ->addArgument('input', InputArgument::REQUIRED, 'Path to Swagger/OpenAPI file with specifications, it may be JSON or YAML')
            ->addArgument('output', InputArgument::OPTIONAL, 'Destination folder', $this->config->output)
            ->setCode(function (InputInterface $input, OutputInterface $output) {

                $io = new SymfonyStyle($input, $output);

                // Save config
                if (null !== $input->getOption('namespace')) {
                    $this->config->namespace = $input->getOption('namespace');
                }
                if (null !== $input->getArgument('input')) {
                    $this->config->input = $input->getArgument('input');
                }
                if (null !== $input->getArgument('output')) {
                    $this->config->output = $input->getArgument('output');
                }

                $io->writeln('Reading information from file: ' . $this->config->input);

                // Read content from remote
                $source = file_get_contents($this->config->input);

                // If source is empty then error
                if (false === $source) {
                    throw new \InvalidArgumentException('Incorrect sources file');
                }

                // If source is not JSON or YAML then error
                if (!$this->isJson($source) || !$this->isYaml($source)) {
                    throw new \InvalidArgumentException('Incorrect format of file');
                }

                /*
                 * Read version of OpenAPI, then select factory for work
                 */

                if (!empty($this->content['swagger']) && $this->content['swagger'] === '2.0') {
                    $model = \OpenChill\Generators\v2\Models::class;
                } elseif (!empty($this->content['openapi']) && $this->content['openapi'] === '3.0.0') {
                    $model = \OpenChill\Generators\v3\Models::class;
                } else {
                    throw new \ErrorException('Version of provided specification is not supported');
                }

                /*
                 * Generate composer.json
                 */

                // Write composer.json file
                ComposerJson::factory($this->config);

                /*
                 * Generate models
                 */

                // Parse and write models
                $model::factory($this->config, $this->content);

                /*
                 * Generate endpoints
                 */

                $io->success('Done');

            })
            ->getApplication()
            ->run();
    }


    // Read endpoints for classes

    // Generate classes with models


    // Generate config class for client

    // Generate http client based on guzzle

}

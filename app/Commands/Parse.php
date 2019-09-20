<?php

namespace OpenChill\Commands;

use OpenChill\Config;
use OpenChill\Generators\Models;
use OpenChill\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Parse extends Command
{
    use Helper;

    protected static $defaultName = 'parse';

    private $config;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->config = new Config();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Parse Swagger/OpenAPI config to PHP client')
            ->addOption('namespace', 's', InputArgument::OPTIONAL, 'Name of root namespace')
            ->addArgument('input', InputArgument::REQUIRED, 'Path to Swagger/OpenAPI file with specifications')
            ->addArgument('output', InputArgument::OPTIONAL, 'Destination folder');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        // Parse models
        $models = Models::factory($this->config, $this->content);

        $io->success('Done');
    }
}

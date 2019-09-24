<?php

namespace OpenChill\Generators;

use ComposerJson\Generator;
use ComposerJson\Schemas\Composer;
use ComposerJson\Schemas\Author;
use ComposerJson\Schemas\Psr4;
use OpenChill\Config;

class ComposerJson
{
    public static function factory(Config $config): bool
    {
        // Initiate composer object
        $composer = new Composer();

        /*
         * Set basic parameters of new composer.json file
         */

        if (isset($config->name)) {
            $composer->name = $config->name;
        }

        if (isset($config->description)) {
            $composer->description = $config->description;
        }

        $composer->type     = $config->type;
        $composer->keywords = ['openapi', 'api-client', 'openchill-codegen'];
        $composer->license  = $config->license;

        /*
         * Autoloader details
         */

        // For normal usage
        $psr4 = new Psr4();

        $psr4->options = [
            $config->namespace . "\\" => './src/',
        ];

        $composer->autoload[] = $psr4;

        // For tests
        $psr4 = new Psr4();

        $psr4->options = [
            $config->namespace . "\\Tests\\" => './tests/',
        ];

        $composer->autoloadDev[] = $psr4;

        /*
         * Authors of project
         */

        $author       = new Author();
        $author->name = $config->author;

        $composer->authors[] = $author;

        /*
         * Require rules
         */

        $composer->require = [
            'php'      => '^7.2',
            // TODO: detect type of data, add json, yaml, xml etc by config in OpenAPI
            'ext-json' => '*'
        ];

        $composer->requireDev = [
            'phpunit/phpunit' => '^8.0',
        ];

        /*
         * Load composer into the generator
         */
        // Initial the generator
        $generator = new Generator();
        $generator->load($composer);

        /*
         * Generate result
         */
        $json = $generator->toJson();

        /*
         * Save file
         */

        // Create folder by class namespace if not exist
        if (!file_exists($config->output)) {
            mkdir($config->output, 0755, true);
        }

        // Save file by path
        file_put_contents($config->output . DIRECTORY_SEPARATOR . 'composer.json', $json);

        return true;
    }
}

<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use OpenChill\Commands\Parse;

$application = new Application();

$application->addCommands([
    new Parse(),
]);

$application->run();

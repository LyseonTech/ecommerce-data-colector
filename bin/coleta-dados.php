#!/usr/bin/env php
<?php

if (PHP_SAPI !== 'cli') {
    echo 'Warning: Should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}

require __DIR__.'/../src/bootstrap.php';

use ColetaDados\Console\Application;
use ColetaDados\Command\ColetaCommand;

error_reporting(-1);

// run the command application
$application = new Application();
$command = new ColetaCommand();
$application->add($command);
$application->setDefaultCommand($command->getName());
$application->run();
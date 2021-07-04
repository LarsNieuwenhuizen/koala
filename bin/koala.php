#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LarsNieuwenhuizen\Koala\Command\CreateProjectCommand;
use LarsNieuwenhuizen\Koala\Command\RestartEnvironmentCommand;
use LarsNieuwenhuizen\Koala\Command\StartEnvironmentCommand;
use LarsNieuwenhuizen\Koala\Command\StopEnvironmentCommand;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function getHelp()
    {
        return "<comment>
========================================================

     ▄▄▄   ▄ ▄▄▄▄▄▄▄ ▄▄▄▄▄▄▄ ▄▄▄     ▄▄▄▄▄▄▄
    █   █ █ █       █       █   █   █       █
    █   █▄█ █   ▄   █   ▄   █   █   █   ▄   █
    █      ▄█  █ █  █  █▄█  █   █   █  █▄█  █
    █     █▄█  █▄█  █       █   █▄▄▄█       █
    █    ▄  █       █   ▄   █       █   ▄   █
    █▄▄▄█ █▄█▄▄▄▄▄▄▄█▄▄█ █▄▄█▄▄▄▄▄▄▄█▄▄█ █▄▄█

========================================================
        \n</comment>" . parent::getHelp();
    }
}

$application = new App();
$application->addCommands([
    new StartEnvironmentCommand(),
    new StopEnvironmentCommand(),
    new RestartEnvironmentCommand(),
    new CreateProjectCommand()
]);

$application->run();

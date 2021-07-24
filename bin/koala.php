#!/usr/bin/env php
<?php

require_once $_SERVER['HOME'] . '/.koala/vendor/autoload.php';

use LarsNieuwenhuizen\Koala\Command\ComposeServiceCommand;
use LarsNieuwenhuizen\Koala\Command\CreateProjectCommand;
use LarsNieuwenhuizen\Koala\Command\RestartEnvironmentCommand;
use LarsNieuwenhuizen\Koala\Command\StartEnvironmentCommand;
use LarsNieuwenhuizen\Koala\Command\StopEnvironmentCommand;
use LarsNieuwenhuizen\Koala\Command\UpdateCommand;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function getHelp(): string
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


$version = \trim(\file_get_contents(
    \getenv('HOME') . DIRECTORY_SEPARATOR . '.koala' . DIRECTORY_SEPARATOR . 'version'
));
$application = new App('Koala', $version);

$application->addCommands([
    new StartEnvironmentCommand(),
    new StopEnvironmentCommand(),
    new RestartEnvironmentCommand(),
    new CreateProjectCommand(),
    new ComposeServiceCommand(),
    new UpdateCommand()
]);

$application->run();

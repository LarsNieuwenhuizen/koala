<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class StartEnvironmentCommand extends Command
{

    protected function configure()
    {
        $this->setName('start')
            ->setDescription('Start the Koala network');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $io->section('Starting containers:');
            \shell_exec('~/.koala/bin/start-environment.sh');
            $io->newLine();
            $io->success('Koala is up and running!');
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }
        return 0;
    }
}

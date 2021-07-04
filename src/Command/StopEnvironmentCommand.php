<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class StopEnvironmentCommand extends Command
{

    protected function configure()
    {
        $this->setName('stop')
            ->setDescription('Start the Koala network');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->section('Shutting down:');
            \shell_exec('$HOME/.koala/bin/stop-environment.sh');
            $io->newLine();
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }

        $io->success('Koala has stopped');
        return 0;
    }
}

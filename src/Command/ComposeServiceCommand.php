<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ComposeServiceCommand extends Command
{

    protected function configure()
    {
        $this->setName('compose:service')
            ->setDescription('Start other services');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            \shell_exec('$HOME/.koala/bin/compose-service.sh');
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

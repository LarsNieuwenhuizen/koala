<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RestartEnvironmentCommand extends Command
{

    protected function configure()
    {
        $this->setName('restart')
            ->setDescription('Re-start the Koala network');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->getApplication()->find('stop')->run($input, $output);
            $this->getApplication()->find('start')->run($input, $output);
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }

        return 0;
    }
}

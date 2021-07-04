<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class CreateProjectCommand extends Command
{

    private Filesystem $filesystem;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->filesystem = new Filesystem();
    }

    protected function configure()
    {
        $this->setName('create:project')
            ->setDescription('Create an empty php/nginx project running on the Koala network');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $domain = new Question("Enter the domain to work on. It will become <comment>{{domain}}</comment>.docker: \n\n");
        $domain = $helper->ask($input, $output, $domain);

        try {
            $this->createProjectDirectory($domain);
            $io->info([
                'Add these lines to your /etc/hosts',
                "127.0.01 $domain.docker",
                "::1 $domain.docker"
            ]);
            $io->comment('The directory is created at ~/koala-projects/' . $domain);
            $io->info([
                'To start:',
                'cd ~/koala-projects/' . $domain,
                'bin/start-environment.sh',
                'Head over to https://' . $domain . '.docker/ in your browser'
            ]);

        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }

        return Command::SUCCESS;
    }

    private function createProjectDirectory(string $directoryName): void
    {
        $home = \getenv('HOME');
        $path = $home . '/koala-projects/' . $directoryName;

        $this->filesystem->mirror(
            $home . '/.koala/templates/newProject',
            $path
        );

        $fileContents = \file_get_contents($path . '/.env.dist');
        $newFileContents = \str_replace(
            '{{projectName}}',
            $directoryName,
            $fileContents
        );
        \file_put_contents($path . '/.env.dist', $newFileContents);
    }
}

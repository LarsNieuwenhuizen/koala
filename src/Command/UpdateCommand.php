<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class UpdateCommand extends Command
{

    private Filesystem $filesystem;

    public function __construct(string $name = null)
    {
        $this->filesystem = new Filesystem();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('self-update')
            ->setDescription('Update to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $installedVersion = \trim(
            \file_get_contents(
                \getenv('HOME') . '/.koala-dev/version'
            )
        );

        $allRemoteTags = \shell_exec(
            "git ls-remote https://github.com/LarsNieuwenhuizen/koala | grep -o 'refs/tags/[0-9]*\.[0-9]*\.[0-9]*' | sort -r | head | grep -o '[^\/]*$'"
        );

        $allRemoteTags = \array_filter(
            \array_unique(
                \explode(
                    "\n",
                    $allRemoteTags
                )
            ),
            'strlen'
        );

        $nextVersion = null;
        foreach ($allRemoteTags as $key => $version) {
            if (\version_compare($installedVersion, $version, '<')) {
                $nextVersion = $version;
                break;
            }
        }

        if ($nextVersion === null) {
            $io->success("You're already on the latest version");
            return Command::SUCCESS;
        }


        $io->comment('Starting update...');
        $this->installNewVersion($nextVersion, $io);
        $this->switchSymlink($nextVersion, $io);
//        $this->replaceConsole();

        return Command::SUCCESS;
    }

    private function installNewVersion(string $version, SymfonyStyle $io): void
    {

        $io->comment('Check if directory does not already exists...');
        $existingDirectory = $this->filesystem->exists(getenv('HOME') . "/.koala-$version");
        if ($existingDirectory === true) {
            $io->error("The directory " . getenv('HOME') . "/.koala-$version already exists");
            exit(1);
        }

        $io->comment('Downloading...');
        \shell_exec(
            "wget -qO koala-$version.zip https://github.com/LarsNieuwenhuizen/koala/archive/refs/tags/$version.zip"
        );

        $io->comment('Unpacking...');
        \shell_exec(
            "unzip koala-$version"
        );

        $io->comment('Install new version...');
        $this->filesystem->mirror("koala-$version", \getenv('HOME') . "/.koala-$version");

        $io->comment('Cleaning up downloaded resources...');
        $this->filesystem->remove("koala-$version.zip");
        $this->filesystem->remove("koala-$version");
    }

    private function switchSymlink(string $version, SymfonyStyle $io)
    {
        $this->filesystem->symlink(getenv('HOME') . "/.koala");
    }
}

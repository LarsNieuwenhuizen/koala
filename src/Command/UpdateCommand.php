<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use LarsNieuwenhuizen\Koala\Exception\InstallUpdateException;
use LarsNieuwenhuizen\Koala\Exception\SwitchSymlinkException;
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
        $result = Command::SUCCESS;
        $installedVersion = \trim(
            \file_get_contents(
                \getenv('HOME') . '/.koala/version'
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
        foreach ($allRemoteTags as $version) {
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
        try {
            $this->installNewVersion($nextVersion, $io);
            $this->switchSymlink($nextVersion, $io);
        } catch (InstallUpdateException $installUpdateException) {
            $io->error($installUpdateException->getMessage());
            $result = Command::FAILURE;
        } catch (SwitchSymlinkException $switchSymlinkException) {
            $io->error("Update failed: " . $switchSymlinkException->getMessage());
            $result = Command::FAILURE;
        }  catch (\Exception $exception) {
            $io->error("Update failed: " . $exception->getMessage());
            $result = Command::FAILURE;
        } finally {
            $this->cleanupResources($nextVersion, $io);
        }

        return $result;
    }

    private function installNewVersion(string $version, SymfonyStyle $io): void
    {
        $home = \getenv('HOME');
        $io->comment('Check if directory does not already exists...');
        $existingDirectory = $this->filesystem->exists(getenv('HOME') . "/.koala-$version");

        if ($existingDirectory === true) {
            $io->error("The directory " . getenv('HOME') . "/.koala-$version already exists");
            throw new InstallUpdateException('Version directory already exists');
        }

        try {
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

            $io->comment('Composer install');
            \shell_exec("cd $home/.koala-$version && composer install --no-interaction -o -q --no-progress");
        } catch (\Exception $exception) {
            throw new InstallUpdateException($exception->getMessage());
        }
    }

    private function cleanupResources(string $version, SymfonyStyle $io): void
    {
        $io->comment('Cleaning up downloaded resources...');
        $this->filesystem->remove("koala-$version.zip");
        $this->filesystem->remove("koala-$version");
    }

    private function switchSymlink(string $version, SymfonyStyle $io): void
    {
        try {
            $io->comment('Switch symlink');
            $home = \getenv('HOME');
            $this->filesystem->remove("$home/.koala");
            $this->filesystem->symlink(
                "$home/.koala-$version",
                "$home/.koala"
            );
        } catch (\Exception $exception) {
            throw new SwitchSymlinkException();
        }
    }
}

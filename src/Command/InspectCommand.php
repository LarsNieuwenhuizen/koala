<?php
declare(strict_types=1);

namespace LarsNieuwenhuizen\Koala\Command;

use LarsNieuwenhuizen\Koala\Exception\ContainerDetailsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InspectCommand extends Command
{

    protected function configure()
    {
        $this->setName('inspect')
            ->setDescription('Show running containers in the Koala network');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dockerInspectData = \json_decode(
            \shell_exec('docker inspect koala')
        );

        if (\array_key_exists(0, $dockerInspectData) === false) {
            throw new \Exception('No data available, did you start Koala?');
        }

        $dockerInspectData = (array)$dockerInspectData[0];

        if (\array_key_exists('Containers', $dockerInspectData) === false) {
            throw new \Exception('No containers in the network it seems, did you start Koala?');
        }

        try {
            $containers = $dockerInspectData['Containers'];

            $rows = [];
            foreach ($containers as $container) {
                try {
                    $ports = $this->getContainerDetails($container->Name)['ports'];
                } catch (ContainerDetailsException $containerDetailsException) {
                    continue;
                }
                $rows[] = [
                    $container->Name,
                    $ports
                ];
            }

            \sort($rows);

            $io->section('The containers running in the Koala network');
            $io->table(['Name', 'Ports (host:container)'], $rows);

            $io->note(
                'View details for the running project webservices in the Traefik dashboard >> http://localhost:8000'
            );

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * @throws ContainerDetailsException
     */
    private function getContainerDetails(string $containerName): array
    {
        $containerDetails = \json_decode(
            \shell_exec('docker inspect ' . $containerName)
        );

        if (\array_key_exists(0, $containerDetails) === false) {
            throw new ContainerDetailsException('No details for the container');
        }

        $details = (array)$containerDetails[0];

        if (\array_key_exists('HostConfig', $details) === false) {
            throw new ContainerDetailsException('No host config for the container');
        }

        $portBindings = (array)$details['HostConfig']->PortBindings;

        $ports = '';
        foreach ($portBindings as $containerPort => $details) {
            $details = \reset($details);
            $hostPort = $details->HostPort;
            $ports .= "$hostPort:$containerPort ";
        }

        return ['ports' => $ports];
    }
}

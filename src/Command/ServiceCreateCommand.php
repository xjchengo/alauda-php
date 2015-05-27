<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;
use Symfony\Component\Yaml\Parser;
use Exception;

class ServiceCreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('service:create')
            ->setDescription('Create a alauda service from your docker-compose.yml file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $output->writeln("<info>only support specifying `image`, `ports`, `environment`</info>");
        $yamlDir = realpath('.');
        $yamlPath = $yamlDir . '/docker-compose.yml';
        if (!file_exists($yamlPath)) {
            $output->writeln("<error>docker-compose.yml file not found in $yamlPath.</error>");
            exit(1);
        }

        $parser = new Parser();
        $compose = $parser->parse(file_get_contents($yamlPath));
        if ($output->isVeryVerbose()) {
            $output->writeln("<info>we parse your yml as following:</info>");
            echo json_encode($compose, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . "\n";
        }

        $payload = [
            'instance_size' => 'XS',
            'scaling_mode' => 'MANUAL',
            'target_state' => 'STARTED',
            'target_num_instances' => '1',
        ];
        foreach ($compose as $serviceName => $service) {
            $payload['service_name'] = $serviceName;
            if (!strpos($service['image'], ':')) {
                $service['image'] .= ':latest';
            }
            list($payload['image_name'], $payload['image_tag']) = explode(':', $service['image']);
            $ports = [];
            foreach ($service['ports'] as $port) {
                if (strpos($port, ':')) {
                    throw new Exception('you can only specify container port. service port is not supported');
                }
                $ports[] = [
                    'protocol' => 'tcp',
                    'container_port' => intval($port)
                ];
            }
            $payload['instance_ports'] = $ports;

            $env = [];
            foreach ($service['environment'] as $envString) {
                if (!strpos($envString, '=')) {
                    $env[$envString] = '';
                } else {
                    list($envKey, $envValue) = explode('=', $envString);
                    $envKey = trim($envKey);
                    $envValue = trim($envValue);
                    $env[$envKey] = $envValue;
                }
            }
            $payload['instance_envvars'] = $env;
            if ($output->isVerbose()) {
                $output->writeln("<info>The full JSON format of the service create request is as follows:</info>");
                echo json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . "\n";
            }

            $result = ApiV1::createService($token['username'], $payload, $token['token']);
            $output->writeln("<info>The service ${payload['service_name']} is deploying...</info>");
        }
        $output->writeln("<info>you can use `service:list` to see the deploy status.</info>");
    }
}

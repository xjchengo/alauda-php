<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class ServiceDestroyCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('service:destroy')
            ->setDescription('Destroy an service, all instances and related resources will be removed.')
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'token',
                InputArgument::OPTIONAL,
                'your alauda token'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $namespace = $input->getArgument('namespace');
        $serviceName = $input->getArgument('service_name');
        $result = ApiV1::destroyService($namespace, $serviceName, $token);
        var_dump($result);

        // // basic info
        // $outputArray = [];
        // foreach ($service as $key => $value) {
        //     if (in_array($key, ['instance_envvars', 'instance_ports', 'instances'])) {
        //         continue;
        //     }
        //     $outputArray[] = [$key, Util::toString($value)];
        // }
        // $table = new Table($output);
        // $table
        //     ->setHeaders(['BasicKey', 'Value'])
        //     ->setRows($outputArray)
        // ;
        // $table->render();

        // // instance_envvars
        // $outputArray = [];
        // $envVars = json_decode($service['instance_envvars'], true);
        // foreach ($envVars as $key => $value) {
        //     $outputArray[] = [$key, Util::toString($value)];
        // }
        // $table = new Table($output);
        // $table
        //     ->setHeaders(['environment_variableName', 'environment_variableValue'])
        //     ->setRows($outputArray)
        // ;
        // $table->render();

        // // instance_ports
        // $headers = ['service_port', 'protocol', 'container_port'];
        // $outputArray = [];
        // $instancePorts = $service['instance_ports'];
        // foreach ($instancePorts as $port) {
        //     $row = [];
        //     foreach ($headers as $header) {
        //         $row[] = $port[$header];
        //     }
        //     $outputArray[] = $row;
        // }
        // $table = new Table($output);
        // $table
        //     ->setHeaders(['service_port', 'protocol', 'container_port'])
        //     ->setRows($outputArray)
        // ;
        // $table->render();
    }
}

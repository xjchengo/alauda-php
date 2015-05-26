<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class InstanceDescribeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('instance:describe')
            ->setDescription('Get the details of an instance.')
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'instance_uuid',
                InputArgument::REQUIRED,
                'you can get it by instance:list command'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $serviceName = $input->getArgument('service_name');
        $uuid = $input->getArgument('instance_uuid');
        $instance = ApiV1::getInstance($token['username'], $serviceName, $uuid, $token['token']);

        // basic info
        $outputArray = [];
        foreach ($instance as $key => $value) {
            if (in_array($key, ['instance_ports'])) {
                continue;
            }
            $outputArray[] = [$key, Util::toString($value)];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['BasicKey', 'Value'])
            ->setRows($outputArray)
        ;
        $table->render();

        // instance_ports
        $headers = ['service_port', 'protocol', 'container_port'];
        $outputArray = [];
        $instancePorts = $instance['instance_ports'];
        foreach ($instancePorts as $port) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = $port[$header];
            }
            $outputArray[] = $row;
        }
        $table = new Table($output);
        $table
            ->setHeaders(['service_port', 'protocol', 'container_port'])
            ->setRows($outputArray)
        ;
        $table->render();
    }
}

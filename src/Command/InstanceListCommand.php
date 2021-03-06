<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class InstanceListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('instance:list')
            ->setDescription('List all instances belong to the application')
            ->addArgument(
                'service_name',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $serviceName = $input->getArgument('service_name');
        $instances = ApiV1::getInstances($token['username'], $serviceName, $token['token']);

        $headers = ['instance_name', 'uuid', 'started_at'];
        $outputArray = [];
        foreach ($instances as $instance) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = Util::toString($instance[$header]);
            }
            $outputArray[] = $row;
        }
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($outputArray)
        ;
        $table->render();
    }
}

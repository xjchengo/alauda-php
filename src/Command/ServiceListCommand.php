<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class ServiceListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('service:list')
            ->setDescription('List all services in a namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $services = ApiV1::getServicesWithServicePort($token['username'], $token['token']);

        $headers = ['service_name', 'image_name', 'default_domain_name', 'instance_ports', 'is_deploying', 'target_state', 'updated_at'];
        $outputArray = [];
        foreach ($services as $service) {
            $row = [];
            foreach ($headers as $header) {
                if ($header == 'instance_ports') {
                    $ports = '';
                    foreach ($service[$header] as $port) {
                        if ($ports) {
                            $ports .= ',' . $port['service_port'] . '->' . $port['container_port'];
                        } else {
                            $ports = $port['service_port'] . '->' . $port['container_port'];
                        }
                    }
                    $row[] = $ports;
                } else {
                    $row[] = Util::toString($service[$header]);
                }
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

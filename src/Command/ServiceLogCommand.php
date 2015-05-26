<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class ServiceLogCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('service:logs')
            ->setDescription('Get service log')
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
        $logs = ApiV1::getServiceLogs($token['username'], $serviceName, $token['token']);
        $logsArray = explode("\n", $logs);
        foreach ($logsArray as $log) {
            if ($log) {
                $output->writeln('<info>' . $log . '</info>');
            }
        }
    }
}

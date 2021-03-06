<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ServiceDestroyCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('service:destroy')
            ->setDescription('Destroy an service, all instances and related resources will be removed.')
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
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("Do you really want to destroy $serviceName (y/n)?", false);
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>action canceled!</info>');
            exit(1);
        }
        $result = ApiV1::destroyService($token['username'], $serviceName, $token['token']);

        $output->writeln('<info>Destroy service ' . $serviceName . ' successfully!</info>');
    }
}

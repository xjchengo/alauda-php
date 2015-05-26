<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xjchen\Alauda\Util;

class LogoutCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('logout')
            ->setDescription('Log out from alauda registry server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = Util::clearToken();
        if ($status) {
            $output->writeln('<info>Logout successfully!</info>');
        } else {
            $output->writeln('<error>Clear the token failed!</error>');
        }
    }
}

<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xjchen\Alauda\Util;

class TokenClearCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('token:clear')
            ->setDescription('Clear the token saved on your home directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = Util::clearToken();
        if ($status) {
            $output->writeln('<info>Clear the token successfully!</info>');
        } else {
            $output->writeln('<error>Clear the token failed!</error>');
        }
    }
}

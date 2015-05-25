<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        file_put_contents($this->getConfigFile(), '');
        $output->writeln('<info>Clear the token successfully!</info>');
    }
}

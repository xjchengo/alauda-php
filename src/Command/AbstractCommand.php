<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Xjchen\Alauda\Util;

abstract class AbstractCommand extends Command
{
    
    protected function getToken(InputInterface $input, OutputInterface $output)
    {
        $token = Util::getToken();
        if (!$token) {
            $output->writeln('<info>you did not login</info>');
            $output->writeln('<info>use `login` command to login</info>');
            exit(1);
        }
        return $token;
    }
}

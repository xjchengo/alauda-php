<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class TokenGenerateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('token:generate')
            ->setDescription('Generate your alauda token.')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'alauda account username'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'alauda account password'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $result = ApiV1::generateToken($username, $password);
        $token = $result['token'];
        Util::saveToken($token, $username);
        $output->writeln('<info>Generate the token successfully!</info>');
    }
}

<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;
use Symfony\Component\Console\Question\Question;

class LoginCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('login')
            ->setDescription('Log in to alauda registry server')
            ->addOption(
                'username',
                'u',
                InputOption::VALUE_REQUIRED,
                'alauda account username'
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_REQUIRED,
                'alauda account password'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $username = $input->getOption('username');
        if (!$username) {
            $question = new Question('Username:', '');
            while (!$username) {
                $username = $helper->ask($input, $output, $question);
            }
        }

        $password = $input->getOption('password');
        if (!$password) {
            $question = new Question('Password:', '');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            while (!$password) {
                $password = $helper->ask($input, $output, $question);
            }
        }

        $result = ApiV1::generateToken($username, $password);
        $token = $result['token'];
        Util::saveToken($token, $username);
        $output->writeln('<info>Login successfully!</info>');
    }
}

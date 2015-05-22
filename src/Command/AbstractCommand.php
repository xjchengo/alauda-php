<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

abstract class AbstractCommand extends Command
{
    protected function getConfigFile()
    {
        $configFile = getenv("HOME") . DIRECTORY_SEPARATOR . '.alauda';
        return $configFile;
    }

    protected function getToken(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');
        $configFile = $this->getConfigFile();
        if (!$token) {
            if (file_exists($configFile)) {
                $token = file_get_contents($configFile);
            }
            $helper = $this->getHelper('question');
            if ($token) {
                $question = new ConfirmationQuestion('Use the token:' . $token . '?', true);
                $useSavedToken = $helper->ask($input, $output, $question);
            }
            if (!$token or !$useSavedToken) {
                $token = '';
                $question = new Question('Please enter the token of your alauda account:');
                while (!$token) {
                    $token = $helper->ask($input, $output, $question);
                }
            }
        }
        file_put_contents($configFile, $token);
        return $token;
    }
}

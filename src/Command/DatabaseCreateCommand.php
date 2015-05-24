<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;
use PDOException;

class DatabaseCreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('db:create')
            ->setDescription('Create a database if not exist.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'host',
                InputArgument::REQUIRED
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_REQUIRED,
                'database port',
                3306
            )
            ->addArgument(
                'user',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbName = $input->getArgument('name');
        $dbHost = $input->getArgument('host');
        $dbPort = $input->getOption('port');
        $dbUser = $input->getArgument('user');
        $dbPassword = $input->getArgument('password');
        
        $dbHandle = new PDO('mysql:host=' . $dbHost . ';port=' . $dbPort, $dbUser, $dbPassword);
        $dbHandle->exec("CREATE DATABASE `$dbName`;") ;
        $output->writeln('<info>Create database ' . $dbName . ' successfully!</info>');
    }
}

<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use PDO;
use PDOException;

class DatabaseUserCreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('db:create-user')
            ->setDescription('Create a database user if not exist')
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
            ->addOption(
                'new_user',
                null,
                InputOption::VALUE_REQUIRED,
                'new database user'
            )
            ->addOption(
                'new_password',
                null,
                InputOption::VALUE_REQUIRED,
                'new database user'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbHost = $input->getArgument('host');
        $dbPort = $input->getOption('port');
        $dbUser = $input->getArgument('user');
        $dbPassword = $input->getArgument('password');

        $helper = $this->getHelper('question');

        $newDbUser = $input->getOption('new_user');
        if (!$newDbUser) {
            $question = new Question('new user name:', '');
            while (!$newDbUser) {
                $newDbUser = $helper->ask($input, $output, $question);
            }
        }

        $newDbPassword = $input->getOption('new_password');
        if (!$newDbPassword) {
            $question = new Question('new user password:', '');
            while (!$newDbPassword) {
                $newDbPassword = $helper->ask($input, $output, $question);
            }
        }
        
        $dbHandle = new PDO('mysql:host=' . $dbHost . ';port=' . $dbPort, $dbUser, $dbPassword);
        $dbHandle->exec("CREATE USER '$newDbUser'@'%' IDENTIFIED BY '$newDbPassword';");
        $dbHandle->exec("GRANT ALL ON *.* TO '$newDbUser'@'%';");
        $dbHandle->exec("FLUSH PRIVILEGES;");
        $output->writeln("<info>Create user $newDbUser:$newDbPassword successfully!</info>");
    }
}

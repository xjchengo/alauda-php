<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;
use Xjchen\Alauda\Config\Factory as ConfigFactory;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Exception;
use PDO;

class UpCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('up')
            ->setDescription('Deploy a php web server in alauda')
            ->addOption(
               'framework',
               'f',
               InputOption::VALUE_NONE,
               'Deployment will be easy when specifying framework'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);

        // choose the framework
        $framework = $input->getOption('framework');
        if (!$framework) {
            $framework = ConfigFactory::guessFramework();
        }
        $needSelect = true;
        $helper = $this->getHelper('question');
        if (in_array(strtolower($framework), ConfigFactory::$supportedFramework)) {
            $question = new ConfirmationQuestion("Do you want to deploy a $framework project(if you want to deploy an reverse proxy server or other framework project, type n)?", true);

            if ($helper->ask($input, $output, $question)) {
                $needSelect = false;
            }
        }
        if ($needSelect) {
            $question = new ChoiceQuestion(
                'Please select your framework (defaults to laravel)',
                array_merge(ConfigFactory::$supportedFramework, ['reverse_proxy', 'others']),
                0
            );
            $question->setErrorMessage('Framework %s is invalid.');
            $framework = $helper->ask($input, $output, $question);
        }

        if ($framework != 'reverse_proxy') {
            // input repository url
            $env['REPOSITORY_URL'] = '';
            $question = new Question('Input your project\'s git repository url(https):', '');
            $env['REPOSITORY_URL'] = $helper->ask($input, $output, $question);

            // choose database
            $question = new ConfirmationQuestion("Use the mysql server in alauda(y/n)?", true);
            if ($helper->ask($input, $output, $question)) {
                $useDefaultMysql = true;
                try {
                    $mysqlService = ApiV1::getService($token['username'], ConfigFactory::MYSQL_CONTAINER, $token['token']);
                } catch (Exception $e) {
                    $question = new Question('Input mysql root password you want to use:', '');
                    $mysqlRootPassword = '';
                    while (!$mysqlRootPassword) {
                        $mysqlRootPassword = $helper->ask($input, $output, $question);
                    }
                    
                    $payload = [
                        'service_name' => ConfigFactory::MYSQL_CONTAINER,
                        'image_name' => 'index.alauda.cn/alauda/mysql',
                        'image_tag' => 'latest',
                        'instance_size' => 'XS',
                        'scaling_mode' => 'MANUAL',
                        'target_state' => 'STARTED',
                        'target_num_instances' => '1',
                        'instance_envvars' => [
                            'MYSQL_ROOT_PASSWORD' => (string)$mysqlRootPassword
                        ],
                        'instance_ports' => [
                            [
                                'container_port' => 3306,
                                'protocol' => 'tcp',
                            ],
                        ],
                    ];
                    $result = ApiV1::createService($token['username'], $payload, $token['token']);
                    if (!($result == null or $result == 'App mysql-xjc already exists')) {
                        $output->writeln("<error>Deploy mysql server wrong:</error>");
                        $output->writeln("<error>    $result</error>");
                        exit(1);
                    } else {
                        $output->writeln("<info>Mysql root password is $mysqlRootPassword</info>");
                    }
                    $isDeploying = true;
                    while ($isDeploying) {
                        $output->writeln("<info>msyql server is deploying...</info>");
                        sleep(2);
                        $mysqlService = ApiV1::getService($token['username'], ConfigFactory::MYSQL_CONTAINER, $token['token']);
                        $isDeploying = $mysqlService['is_deploying'];
                    }
                }
                $envVars = is_string($mysqlService['instance_envvars'])?json_decode($mysqlService['instance_envvars'], true):$mysqlService['instance_envvars'];
                $env['DB_HOST'] = $envVars['__DEFAULT_DOMAIN_NAME__'];
                $env['DB_PORT'] = (string)$mysqlService['instance_ports'][0]['service_port'];
                $env['DB_USER'] = 'root';
                $env['DB_PASSWORD'] = (string)$envVars['MYSQL_ROOT_PASSWORD'];
            } else {
                $output->writeln("<error>Not supported now!</error>");
                exit(1);
            }
            $configRepo = ConfigFactory::getConfigRepository($framework);
            $projectDbName = $configRepo->getDbName();
            $projectDbHost = $configRepo->getDbHost();
            $projectDbUser = $configRepo->getDbUser();
            $projectDbPassword = $configRepo->getDbUser();
            $output->writeln('<info>We detect you use following database config:</info>');
            $output->writeln('<info>DB_NAME: ' . $projectDbName . '</info>');
            $output->writeln('<info>DB_HOST: ' . $projectDbHost . '</info>');
            $output->writeln('<info>DB_USER: ' . $projectDbUser . '</info>');
            $output->writeln('<info>DB_PASSWORD: ' . $projectDbPassword . '</info>');
            if ($projectDbUser != 'root') {
                $output->writeln("<info>we will create $projectDbUser for you</info>");
                $dbHandle = new PDO('mysql:host=' . $env['DB_HOST'] . ';port=' . $env['DB_PORT'], $env['DB_USER'], $env['DB_PASSWORD']);
                $dbHandle->exec("CREATE USER '$projectDbUser'@'%' IDENTIFIED BY '$projectDbPassword';");
                $dbHandle->exec("GRANT ALL ON *.* TO '$projectDbUser'@'%';");
                $dbHandle->exec("FLUSH PRIVILEGES;");
                $output->writeln("<info>Create user $projectDbUser:$projectDbPassword successfully!</info>");
            }
            $output->writeln("<info>we will forward 127.0.0.1:3306 on php server to mysql server, so if you use 127.0.0.1:3306 as mysql host in your project, you do not need to change your config.</info>");
            $env['DB_NAME'] = $projectDbName;
        }
        $env['ROOT_PASSWORD'] = '';
        $question = new Question('Input php server root password you want to use:', '');
        while (!$env['ROOT_PASSWORD']) {
            $env['ROOT_PASSWORD'] = $helper->ask($input, $output, $question);
        }
        $env['FRAMEWORK'] = $framework;
        $serviceName = 'php-xjc'.time();
        $payload = [
            'service_name' => $serviceName,
            'image_name' => 'index.alauda.cn/xjchengo/php',
            'image_tag' => 'latest',
            'instance_size' => 'XS',
            'scaling_mode' => 'MANUAL',
            'target_state' => 'STARTED',
            'target_num_instances' => '1',
            'instance_envvars' => $env,
            'instance_ports' => [
                [
                    'container_port' => 22,
                    'protocol' => 'tcp',
                ],
                [
                    'container_port' => 80,
                    'protocol' => 'tcp',
                ],
            ],
        ];
        $result = ApiV1::createService($token['username'], $payload, $token['token']);
        $output->writeln("<info>The php server $serviceName is deploying.</info>");
        $output->writeln("<info>you can use `service:list` to see deployment status or `service:logs` to see details.</info>");
    }
}

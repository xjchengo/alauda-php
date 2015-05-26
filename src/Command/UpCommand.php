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

class UpCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('up')
            ->setDescription('Deploy a php web server in alauda.')
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'token',
                InputArgument::OPTIONAL,
                'your alauda token'
            )
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
        $namespace = $input->getArgument('namespace');

        // choose the framework
        $framework = $input->getOption('framework');
        if (!$framework) {
            $framework = ConfigFactory::guessFramework();
        }
        $needSelect = true;
        $helper = $this->getHelper('question');
        if (in_array(strtolower($framework), ConfigFactory::$supportedFramework)) {
            $question = new ConfirmationQuestion("We guess you use $framework ?", true);

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


        // choose database
        $question = new ConfirmationQuestion("Use the mysql server in alauda?", true);
        if ($helper->ask($input, $output, $question)) {
            $useDefaultMysql = true;
            $mysqlService = ApiV1::getService($namespace, ConfigFactory::MYSQL_CONTAINER, $token);
            if (isset($mysqlService['detail'])) {
                $output->writeln("<info>Deploy mysql server...</info>");
                $payload = [
                    'service_name' => ConfigFactory::MYSQL_CONTAINER,
                    'image_name' => 'index.alauda.cn/alauda/mysql',
                    'image_tag' => 'latest',
                    'instance_size' => 'XS',
                    'scaling_mode' => 'MANUAL',
                    'target_state' => 'STARTED',
                    'target_num_instances' => '1',
                    'instance_envvars' => [
                        'MYSQL_ROOT_PASSWORD' => '123456'
                    ],
                    'instance_ports' => [
                        [
                            'container_port' => 3306,
                            'protocol' => 'tcp',
                        ],
                    ],
                ];
                $result = ApiV1::createService($namespace, $payload, $token);
                if (!($result == null or $result == 'App mysql-xjc already exists')) {
                    $output->writeln("<error>Deploy mysql server wrong:</error>");
                    $output->writeln("<error>    $result</error>");
                    exit(1);
                } else {
                    $output->writeln("<info>Mysql root password is 123456</info>");
                }
                $isDeploying = true;
                while ($isDeploying) {
                    $output->writeln("<info>msyql server is deploying...</info>");
                    sleep(2);
                    $mysqlService = ApiV1::getService($namespace, ConfigFactory::MYSQL_CONTAINER, $token);
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
        $output->writeln('<info>DB_NAME: ' . $configRepo->getDbName() . '</info>');
        $env['DB_NAME'] = $configRepo->getDbName();
        $env['ROOT_PASSWORD'] = '123456';
        $env['FRAMEWORK'] = $framework;
        $payload = [
            'service_name' => 'php'.time(),
            'image_name' => 'index.alauda.cn/xjchengo/php',
            'image_tag' => 'latest',
            // 'run_command' => 'supervisord',
            'instance_size' => 'XS',
            'scaling_mode' => 'MANUAL',
            'target_state' => 'STARTED',
            'target_num_instances' => '1',
            'instance_envvars' => $env,
            // Link cannot work currently.
            // "linked_to" => [
            //     "mysql" => ConfigFactory::MYSQL_CONTAINER
            // ],
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
        $result = ApiV1::createService($namespace, $payload, $token);
        $output->writeln("<info>The php server is deploying.</info>");
        $output->writeln("<info>you can type: alauda service:list to see deployment status.</info>");
    }
}

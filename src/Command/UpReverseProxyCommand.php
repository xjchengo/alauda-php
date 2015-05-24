<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class UpReverseProxyCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('up:reverse-proxy')
            ->setDescription('Deploy a reverse proxy server in alauda.')
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'token',
                InputArgument::OPTIONAL,
                'your alauda token'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $namespace = $input->getArgument('namespace');
        $payload = [
            'app_name' => 'nginx888',
            'service_name' => 'nginx888',
            'image_name' => 'index.alauda.cn/xjchengo/nginx-php',
            'image_tag' => 'latest',
            'run_command' => 'supervisord',
            'instance_size' => 'XS',
            'scaling_mode' => 'MANUAL',
            'target_state' => 'STARTED',
            'target_num_instances' => '1',
            'instance_envvars' => [
                'GPG_KEYS' => '6E4F6AB321FDC07F2C332E3AC2BF0BC433CFC8B3 0BD78B5F97500D450838F95DFE857D9A90D90EC1',
                'NGINX_VERSION' => '1.9.0-1~jessie',
                'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
                'PHP_INI_DIR' => '/usr/local/etc/php',
                'PHP_VERSION' => '5.6.9',
                'ROOT_PASSWORD' => '123456',
                'FRAMEWORK' => 'reverse_proxy'
            ],
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
        var_dump($result);
        $output->writeln('<info>The reverse proxy server is deploying. Please wait!<info>');
    }
}

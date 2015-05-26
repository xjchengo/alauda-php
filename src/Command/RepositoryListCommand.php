<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class RepositoryListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('repository:list')
            ->setDescription('List all repositories')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $repositories = ApiV1::getRepositories($token['username'], $token['token']);

        $headers = ['repo_name', 'image_name', 'is_public', 'is_automated', 'repo_starred_count', 'download', 'upload', 'created_at'];
        $outputArray = [];
        foreach ($repositories['results'] as $repository) {
            $row = [];
            foreach ($headers as $header) {
                if ($header == 'image_name') {
                    $row[] = 'index.alauda.cn/' . $token['username'] . '/' . $repository['repo_name'];
                } else {
                    $row[] = Util::toString($repository[$header]);
                }
            }
            $outputArray[] = $row;
        }
        $headers = ['repo_name', 'image_name', 'is_public', 'is_automated', 'star', 'download', 'upload', 'created_at'];
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($outputArray)
        ;
        $table->render();
    }
}

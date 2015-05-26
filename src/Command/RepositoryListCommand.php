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
            ->setDescription('List all repositories.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $repositories = ApiV1::getRepositories($token['username'], $token['token']);

        $headers = ['repo_name', 'description', 'is_public', 'is_automated', 'repo_starred_count', 'download', 'upload', 'created_at', 'updated_at'];
        $outputArray = [];
        foreach ($repositories['results'] as $repository) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = Util::toString($repository[$header]);
            }
            $outputArray[] = $row;
        }
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($outputArray)
        ;
        $table->render();
    }
}

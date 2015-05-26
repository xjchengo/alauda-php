<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;

class RepositoryTagCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('repository:tags')
            ->setDescription('List all tags of a repository.')
            ->addArgument(
                'repo_name',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->getToken($input, $output);
        $repoName = $input->getArgument('repo_name');
        $tags = ApiV1::getRepositoryTags($token['username'], $repoName, $token['token']);

        $headers = ['image_id', 'tag'];
        $outputArray = [];
        foreach ($tags as $tag) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = $tag[$header];
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

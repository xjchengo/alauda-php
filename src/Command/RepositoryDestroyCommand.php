<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;
use Xjchen\Alauda\Util;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RepositoryDestroyCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('repository:destroy')
            ->setDescription('Delete a repository, all tags will be removed')
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
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("Do you really want to destroy $repoName (y/n)?", false);
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>action canceled!</info>');
            exit(1);
        }
        $result = ApiV1::destroyRepository($token['username'], $repoName, $token['token']);

        $output->writeln('<info>Destroy repository ' . $repoName . ' successfully!</info>');
    }
}

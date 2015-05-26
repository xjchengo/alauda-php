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

class RepositoryCreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('repository:create')
            ->setDescription('Create a new Repository.')
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

        $repositories = ApiV1::getRepositories($token['username'], $token['token']);
        foreach ($repositories['results'] as $repository) {
            if ($repository['repo_name'] == $repoName) {
                $output->writeln("<error>The repository $repoName already exists.</error>");
                exit(1);
            }
        }

        $helper = $this->getHelper('question');
        $question = new Question('Description:', 'nothing');
        $description = $helper->ask($input, $output, $question);

        $question = new ChoiceQuestion(
            'Visibility (defaults to public)',
            ['public', 'private'],
            0
        );
        $question->setErrorMessage('%s is invalid.');
        $visibility = $helper->ask($input, $output, $question);
        if ($visibility == 'public') {
            $isPublic = true;
        } else {
            $isPublic = false;
        }
        $output->writeln("<info>Create repository $repoName: $description $isPublic successfully!</info>");
    }
}

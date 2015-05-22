<?php namespace Xjchen\Alauda\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Xjchen\Alauda\Api\V1 as ApiV1;

class AuthProfileCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('auth:profile')
            ->setDescription('Get the profile of a user.')
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
        $profile = ApiV1::getAuthProfile($token);

        $outputArray = [];
        foreach ($profile as $key => $value) {
            $outputArray[] = [$key, $value];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['Key', 'Value'])
            ->setRows($outputArray)
        ;
        $table->render();
    }
}

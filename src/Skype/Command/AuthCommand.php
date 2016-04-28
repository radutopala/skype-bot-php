<?php

namespace Skype\Command;

use Skype\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AuthCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('auth')
            ->setDescription('Auth against Skype API')
            ->addArgument('clientId', InputArgument::REQUIRED, 'Client ID')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Client Secret:', null);
        $question->setHidden(true);
        $question->setMaxAttempts(3);

        $question->setValidator(function ($value) use ($input, $output) {
            if (trim($value) == '') {
                throw new \Exception('The Client Secret can not be empty');
            }

            $client = new Client([
                'clientId' => $input->getArgument('clientId'),
                'clientSecret' => $value,
            ], null, $output);
            $client->auth();

        });

        $helper->ask($input, $output, $question);
    }
}

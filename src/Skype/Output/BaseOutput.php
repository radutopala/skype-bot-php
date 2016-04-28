<?php

namespace Skype\Output;

use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseOutput
{
    public function getApiClass()
    {
        list(, $caller) = debug_backtrace(false);

        return strtr(basename($caller['file']), ['.php'=> '']);
    }

    public function action(OutputInterface $output, $action = 'Created')
    {
        $output->writeln(
            sprintf('<info>%s: %s.</info>', $this->getApiClass(), $action)
        );
    }
}

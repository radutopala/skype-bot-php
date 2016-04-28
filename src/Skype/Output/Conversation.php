<?php

namespace Skype\Output;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Conversation extends BaseOutput
{
    /**
     * @param  OutputInterface   $output
     * @param  ResponseInterface $response
     * @return mixed
     */
    public function activity(OutputInterface $output, ResponseInterface $response)
    {
        $this->action($output, 'Activity created');
    }
}

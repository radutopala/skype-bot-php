<?php

namespace Skype\Authentication;

interface TokenStorageInterface
{
    /**
     * @return mixed
     */
    public function read();

    /**
     * @param  array $config
     * @return mixed
     */
    public function write(array $config);
}

<?php

namespace Skype\Authentication;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class FileTokenStorage implements TokenStorageInterface
{
    /**
     * @var null|string
     */
    private $file;
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @param string $file
     */
    public function __construct($file = null)
    {
        if (null === $file) {
            $file = isset($_SERVER['HOME']) ? $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.skype' : sys_get_temp_dir() . DIRECTORY_SEPARATOR . '.skype';
        }

        $this->file = $file;
        $this->fileSystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function read($key = 'access_token')
    {
        $content = Yaml::parse($this->file);

        return isset($content[$key]) ? $content[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $config)
    {
        $this->fileSystem->dumpFile($this->file, Yaml::dump($config));
    }
}

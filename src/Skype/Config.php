<?php

namespace Skype;

final class Config
{
    /**
     * @var Client id
     */
    private $_clientId;
    /**
     * @var Client secret
     */
    private $_clientSecret;
    /**
     * @var Auth uri
     */
    private $_authUri = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    /**
     * @var Base uri
     */
    private $_baseUri = 'https://apis.skype.com';
    /**
     * @var Http errors
     */
    private $_httpErrors = false;
    /**
     * @var Token storage file path
     */
    private $_fileTokenStoragePath;

    /**
      * @var Token storage interface
      */
     private $_tokenStorageClass;
    /**
     * Constructor
     *
     * @param  array          $data Array of parameters
     * @throws SkypeException Unknown property
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $k => $v) {
            $result = $this->set($k, $v);
            if (!$result) {
                throw new SkypeException('Property [' . $k . '] is unknown or can not be set.');
            }
        }
    }

    /**
     * Set option
     *
     * @param  string  $key   Option name
     * @param  mixed   $value Option value
     * @access  public
     * @return boolean
     */
    public function set($option, $value)
    {
        $name = '_' . $option;

        $r = new \ReflectionClass('\\' . __CLASS__);
        try {
            $r->getProperty($name);
            $this->$name = $value;

            return true;
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Get option
     *
     * @param  string         $option Option name
     * @access  public
     * @return mixed
     * @throws SkypeException Wrong property
     */
    public function get($option)
    {
        $name = '_' . $option;

        if (!property_exists('Skype\Config', $name)) {
            throw new SkypeException('Wrong property name requested.');
        }

        return $this->$name;
    }
}

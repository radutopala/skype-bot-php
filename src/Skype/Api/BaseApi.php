<?php

namespace Skype\Api;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

abstract class BaseApi
{
    /**
     * @var ClientInterface
     */
    protected $client;
    private $token;
    private $logger;

    /**
     * BaseApi constructor.
     * @param ClientInterface $client
     * @param null            $token
     */
    public function __construct(ClientInterface $client, $token = null, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->token  = $token;
        $this->logger = $logger;
    }

    /**
     * @param $method
     * @param  null                                $uri
     * @param  array                               $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function request($method, $uri = null, array $options = [])
    {
        if (null !== $this->token) {
            $options = array_merge($options, [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $this->token)
                ]
            ]);
        }

        $this->log(
            sprintf(
                'Uri: %s, Method: %s, Options: %s',
                $uri,
                $method,
                \GuzzleHttp\json_encode($options)
            )
        );

        $response = $this->client->request($method, $uri, $options);

        return $response;
    }

    /**
     * @param $message
     */
    protected function log($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }
}

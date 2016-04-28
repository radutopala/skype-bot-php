<?php

namespace Skype\Console;

use ArgumentsResolver\NamedArgumentsResolver;
use Camel\CaseTransformerInterface;
use phpDocumentor\Reflection\DocBlock;
use Skype\Api\ApiInterface;
use Skype\Authentication\TokenStorageInterface;
use Skype\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFactory
{
    /**
     * @var CaseTransformerInterface
     */
    private $transformer;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $token, CaseTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
        $this->tokenStorage = $token;
    }

    /**
     * Generates Commands from all the Api Methods.
     *
     * @param array $classes
     *
     * @return Command[]
     */
    public function generateCommands(array $classes = [])
    {
        $classes = $this->readApis($classes);
        $token = $this->tokenStorage->read();
        $commands = [];

        //todo if token is not empty then show the other commands

        foreach ($classes as $class) {
            $api = new \ReflectionClass($class);

            foreach ($api->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (0 !== strpos($method->getName(), '__')) { //skip magics
                    $command = $this->generateCommand($api->getShortName(), $method, $token);
                    $commands[$command->getName()] = $command;
                }
            }
        }

        return $commands;
    }

    /**
     * Creates a Command based on an Api Method.
     *
     * @param string            $name
     * @param \ReflectionMethod $method
     * @param string            $token
     *
     * @return Command
     */
    private function generateCommand($name, \ReflectionMethod $method, $token = null)
    {
        $methodName = $this->transformer->transform($method->getName());

        $command = new Command(strtolower($name . ':' . $methodName));
        $docBlock = new DocBlock($method->getDocComment());

        $command->setDefinition($this->buildDefinition($method, $docBlock, $token));
        $command->setDescription($docBlock->getShortDescription());
        $command->setCode($this->createCode($name, $method));

        return $command;
    }

    /**
     * Builds the Input Definition based upon Api Method Parameters.
     *
     * @param \ReflectionMethod $method
     * @param string            $token
     *
     * @return InputDefinition
     */
    private function buildDefinition(\ReflectionMethod $method, DocBlock $docBlock, $token = null)
    {
        $definition = new InputDefinition();

        foreach ($docBlock->getTags() as $tag) {
            if ($tag instanceof DocBlock\Tag\ParamTag) {
                $tagsDescription[$tag->getVariableName()] = $tag->getDescription();
            }
        }

        foreach ($method->getParameters() as $parameter) {

            if ($parameter->isDefaultValueAvailable()) {
                //option
                $definition->addOption(new InputOption($parameter->getName(), null, InputOption::VALUE_REQUIRED, $tagsDescription['$' . $parameter->getName()], $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null));
            } else {
                //argument
                $definition->addArgument(new InputArgument($parameter->getName(), InputArgument::REQUIRED, $tagsDescription['$' . $parameter->getName()], null));
            }
        }

        $definition->addOption(new InputOption('token', null, InputOption::VALUE_REQUIRED, 'Auth token to use', $token));
        $definition->addOption(new InputOption('debug', null, InputOption::VALUE_NONE, 'Display raw response'));

        return $definition;
    }

    /**
     * Creates the command execution code.
     *
     * @param string            $name
     * @param \ReflectionMethod $method
     *
     * @return \Closure
     */
    private function createCode($name, \ReflectionMethod $method)
    {
        return function (InputInterface $input, OutputInterface $output) use ($name, $method) {
            $client = new Client([], null, $output);

            if ($input->getOption('token')) {
                $client->authorize($input->getOption('token'));
            }

            $methodName = $method->getName();
            $api = $client->api(strtolower($name));
            $args = (new NamedArgumentsResolver($method))->resolve(array_merge($input->getOptions(), $input->getArguments()));
            $outputClass = $this->generateOutputClassFromApiClass($api);

            $response = call_user_func_array([$api, $methodName], $args);

            if (method_exists($outputClass, $methodName) && false === $input->getOption('debug') && $response->getStatusCode() < 400) {
                (new $outputClass())->{$methodName}($output, $response);
            } elseif (!method_exists($outputClass, $methodName) || true === $input->getOption('debug')) {
                $output->writeln(print_r($response->getHeaders(), true) . print_r((string) $response->getBody(), true));
            }
        };
    }

    /**
     * Reads all the Api classes.
     *
     * @param array $classes
     *
     * @return array
     */
    private function readApis(array $classes = [])
    {
        $baseClasses = [
            'Skype\Api\Conversation'
        ];

        return array_merge($baseClasses, $classes);
    }

    /**
     * Remove the last namespace prefix of the Api Class and replace it with Output
     * e.g. Skype\Api\Conversation -> Skype\Output\Conversation.
     *
     * @param ApiInterface $api
     *
     * @return string
     */
    private function generateOutputClassFromApiClass(ApiInterface $api)
    {
        $classParts = explode('\\', get_class($api));
        $apiName = array_pop($classParts);
        array_pop($classParts);

        return implode('\\', $classParts) . '\\Output\\' . $apiName;
    }
}

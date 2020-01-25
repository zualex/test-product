<?php

declare(strict_types=1);

namespace App\Controller\Resolver;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use App\Controller\Base\BaseController;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class ContainerControllerResolver extends ControllerResolver
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param LoggerInterface|null $logger
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->container = $container;

        parent::__construct($logger);
    }

    /**
     * @inheritdoc
     */
    protected function instantiateController($class)
    {
        return $this->configureController(parent::instantiateController($class));
    }

    /**
     * @param $controller
     * @return object
     */
    private function configureController($controller)
    {
        if ($controller instanceof BaseController) {
            $controller->setContainer($this->container);
        }

        return $controller;
    }
}
<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Service\ServiceInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ServiceResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(ServiceInterface::class)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $key = $this->getContainerKey($class);

        yield $this->container->get($key);
    }

    /**
     * @param string $class
     * @return string
     */
    private function getContainerKey(string $class): string
    {
        return str_replace('\\', '.', $class);
    }
}
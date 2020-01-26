<?php

declare(strict_types=1);

namespace App\Controller\Resolver;

use App\Service\ServiceInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ArgumentServiceResolver implements ArgumentValueResolverInterface
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

        yield $this->container->get($class);
    }
}
<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Kernel implements HttpKernelInterface
{
    /**
     * @var UrlMatcher
     */
    protected $matcher;

    /**
     * @var ControllerResolver
     */
    protected $controllerResolver;

    /**
     * @var ArgumentResolver
     */
    protected $argumentResolver;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param UrlMatcher $matcher
     * @param ControllerResolver $controllerResolver
     * @param ArgumentResolver $argumentResolver
     * @param ContainerInterface $container
     */
    public function __construct(
        UrlMatcher $matcher,
        ControllerResolver $controllerResolver,
        ArgumentResolver $argumentResolver,
        ContainerInterface $container
    ) {
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request, int $type = self::MASTER_REQUEST, bool $catch = true)
    {
        $this->beforeHandle($request);

        $this->matcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $exception) {
            return new Response('Not Found', 404);
        } catch (\Exception $exception) {
            return new Response('An error occurred', 500);
        }
    }

    /**
     * Catch request before handle
     *
     * @param Request $request
     */
    protected function beforeHandle(Request $request): void
    {
        if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : []);
        }
    }
}
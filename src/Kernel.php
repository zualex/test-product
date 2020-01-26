<?php

declare(strict_types=1);

namespace App;

use App\DTO\Response\ErrorResponseDTO;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Kernel implements HttpKernelInterface
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $debug;

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
     * @param string $environment
     * @param bool $debug
     * @param UrlMatcher $matcher
     * @param ControllerResolver $controllerResolver
     * @param ArgumentResolver $argumentResolver
     * @param ContainerInterface $container
     */
    public function __construct(
        string $environment,
        bool $debug,
        UrlMatcher $matcher,
        ControllerResolver $controllerResolver,
        ArgumentResolver $argumentResolver,
        ContainerInterface $container
    ) {
        $this->environment = $environment;
        $this->debug = $debug;
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
        } catch (\Throwable $exception) {
            return $this->handleException($exception);
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

    /**
     * Handle exception
     *
     * @param \Throwable $exception
     * @return JsonResponse
     */
    protected function handleException(\Throwable $exception): JsonResponse
    {
        $errorInfo = null;

        if ($this->debug) {
            $errorInfo = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stack-trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        if ($exception instanceof ResourceNotFoundException) {
            return new JsonResponse(new ErrorResponseDTO('resource_missing', 'Not found'), 404);
        }

        if ($exception instanceof BadRequestHttpException) {
            return new JsonResponse(new ErrorResponseDTO('bad_request', $exception->getMessage(), $errorInfo), 400);
        }

        return new JsonResponse(new ErrorResponseDTO('error', 'An error occurred', $errorInfo), 500);
    }
}
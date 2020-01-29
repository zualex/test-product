<?php

declare(strict_types=1);

namespace App\Controller\Base;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @required
     * @param ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }
}
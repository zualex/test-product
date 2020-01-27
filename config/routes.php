<?php

declare(strict_types=1);

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add(
    'v1:product:createRandom',
    (new Routing\Route(
        '/api/v1/product/random',
        ['_controller' => [\App\Controller\V1\ProductController::class, 'createRandom']]
    ))->setMethods('POST')
);

$routes->add(
    'v1:order:create',
    (new Routing\Route(
        '/api/v1/order',
        ['_controller' => [\App\Controller\V1\OrderController::class, 'create']]
    ))->setMethods('POST')
);

$routes->add(
    'v1:order:pay',
    (new Routing\Route(
        '/api/v1/order/{orderId}/pay',
        ['_controller' => [\App\Controller\V1\OrderController::class, 'pay']]
    ))->setMethods('POST')
);

return $routes;
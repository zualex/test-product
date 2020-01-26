<?php

declare(strict_types=1);

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add('product', new Routing\Route('/product/random',
    [
        '_controller' => 'App\Controller\ProductController::createRandom',
    ],
    [],
    [],
    '',
    [],
    ['POST']
));

$routes->add('order', new Routing\Route('/order',
    [
        '_controller' => 'App\Controller\OrderController::create',
    ],
    [],
    [],
    '',
    [],
    ['POST']
));

return $routes;
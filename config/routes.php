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

return $routes;
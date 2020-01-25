<?php

declare(strict_types=1);

require_once __DIR__.'/../config/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;

$routes = include __DIR__.'/../config/routes.php';
$container = include __DIR__.'/../config/containers.php';

$request = Request::createFromGlobals();

$response = $container->get('kernel')->handle($request);

$response->send();
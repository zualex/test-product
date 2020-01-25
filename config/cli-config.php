<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/bootstrap.php';

$routes = include __DIR__.'/../config/routes.php';
$container = include __DIR__.'/../config/containers.php';

return ConsoleRunner::createHelperSet($container->get('entity_manager'));
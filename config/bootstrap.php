<?php

declare(strict_types=1);

require_once  __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;

$paths = [__DIR__ . '/../src/Entity'];
$isDevMode = true;

$dbParams = [
    'driver'   => 'pdo_mysql',
    'url' => 'mysql://local_user:123456@192.168.99.100:3306/test_product',
];

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
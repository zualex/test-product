<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\Validator;

$containerBuilder = new DependencyInjection\ContainerBuilder();
$containerBuilder->register('context', Routing\RequestContext::class);
$containerBuilder->register('matcher', Routing\Matcher\UrlMatcher::class)
    ->setArguments([$routes, new Reference('context')]);

$containerBuilder->register('argument_metadata_factory', HttpKernel\ControllerMetadata\ArgumentMetadataFactory::class);
$containerBuilder->register('validator_builder', Validator\ValidatorBuilder::class)
    ->addMethodCall('addMethodMapping', ['loadValidatorMetadata']);

$validator = Validator\Validation::createValidatorBuilder()
    ->addMethodMapping('loadValidatorMetadata')
    ->getValidator();

$containerBuilder->register('argument_resolver', HttpKernel\Controller\ArgumentResolver::class)
    ->setArguments([
        new Reference('argument_metadata_factory'),
        array_merge(
            HttpKernel\Controller\ArgumentResolver::getDefaultArgumentValueResolvers(),
            [
                new Reference('argument_request_dto_resolver'),
                new Reference('argument_service_resolver'),
            ]
        )
    ]);

$containerBuilder->register('doctrine_config', Doctrine\ORM\Tools\Setup::class)
    ->setFactory([Doctrine\ORM\Tools\Setup::class, 'createAnnotationMetadataConfiguration'])
    ->setArguments([
        [__DIR__ . '/../' . getenv('PATH_TO_ENTITY_FILES')],
        getenv('APP_DEBUG') === 'true',
        null,
        null,
        false
    ]);

$containerBuilder->register('entity_manager', Doctrine\ORM\EntityManager::class)
    ->setFactory([Doctrine\ORM\EntityManager::class, 'create'])
    ->setArguments([
        [
            'driver'   => getenv('DB_DRIVER'),
            'url' => getenv('DB_CONNECTION_URL'),
        ],
        new Reference('doctrine_config')
    ]);

$containerBuilder->register('kernel', Kernel::class)
    ->setArguments([
        getenv('APP_ENV'),
        getenv('APP_DEBUG') === 'true',
        new Reference('matcher'),
        new Reference('controller_resolver'),
        new Reference('argument_resolver'),
        $containerBuilder
    ]);

$containerBuilder->register('http_client', \GuzzleHttp\Client::class)
    ->setArguments([[
        'timeout' => 5
    ]]);

$containerBuilder->register('controller_resolver', \App\Controller\Resolver\ContainerControllerResolver::class)
    ->setArguments([$containerBuilder]);

$containerBuilder->register('argument_request_dto_resolver', App\Controller\Resolver\ArgumentRequestDTOResolver::class)
    ->setArguments([$validator]);

$containerBuilder->register('argument_service_resolver', \App\Controller\Resolver\ArgumentServiceResolver::class)
    ->setArguments([$containerBuilder]);

$containerBuilder->register(\App\Service\Product\ProductService::class, \App\Service\Product\ProductService::class)
    ->setArguments([new Reference('entity_manager')]);

$containerBuilder->register(\App\Service\Status\OrderStatusService::class, \App\Service\Status\OrderStatusService::class)
    ->setArguments([new Reference('entity_manager')]);


$containerBuilder->register(\App\Service\Payment\PaymentService::class, \App\Service\Payment\PaymentService::class)
    ->setArguments([
        new Reference('http_client')
    ]);


$containerBuilder->register(\App\Service\Order\OrderService::class, \App\Service\Order\OrderService::class)
    ->setArguments([
        new Reference('entity_manager'),
        new Reference(\App\Service\Product\ProductService::class),
        new Reference(\App\Service\Status\OrderStatusService::class),
        new Reference(\App\Service\Payment\PaymentService::class),
    ]);

return $containerBuilder;
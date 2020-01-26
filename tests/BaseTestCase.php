<?php

declare(strict_types=1);

namespace Tests;

use Doctrine\ORM\EntityManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BaseTestCase extends TestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var HttpKernelInterface
     */
    protected static $kernel;

    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @var bool
     */
    protected static $booted = false;

    protected function tearDown(): void
    {
        static::ensureKernelShutdown();
        static::$kernel = null;
        static::$booted = false;
    }

    /**
     * Boots the Kernel for this test.
     *
     * @return HttpKernelInterface
     */
    protected static function bootKernel(): HttpKernelInterface
    {
        static::ensureKernelShutdown();

        static::$container = static::createContainer();

        static::$kernel = static::$container->get('kernel');
        static::$booted = true;

        return static::$kernel;
    }

    /**
     * Shuts the kernel down if it was used in the test - called by the tearDown method by default.
     */
    protected static function ensureKernelShutdown(): void
    {
        if (static::$kernel !== null) {
            static::$booted = false;
        }

        static::$container = null;
    }

    protected static function createContainer(): ContainerInterface
    {
        $routes = include __DIR__.'/../config/routes.php';

        return include __DIR__.'/../config/containers.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = FakerFactory::create();
    }

    protected function getEntityManager(): EntityManager
    {
        if (static::$booted === false) {
            self::bootKernel();
        }

        return self::$container->get('entity_manager');
    }
}
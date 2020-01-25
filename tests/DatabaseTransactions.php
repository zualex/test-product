<?php

declare(strict_types=1);

namespace Tests;

use Doctrine\ORM\EntityManager;

trait DatabaseTransactions
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->em = self::$container->get('entity_manager');
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }
}
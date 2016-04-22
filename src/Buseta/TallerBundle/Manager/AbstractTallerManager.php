<?php

namespace Buseta\TallerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractTallerManager
 *
 * @package Buseta\TallerBundle\Manager
 */

abstract class AbstractTallerManager
{
    /**
     * Set if use or not persistent transactions
     *
     * @var boolean
     */
    const USE_TRANSACTIONS = true;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EntityManager            $em
     * @param Logger                   $logger
     * @param EventDispatcherInterface $dispatcher
     */
    function __construct(EntityManager $em, Logger $logger, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Begin transaction if true.
     */
    protected function beginTransaction()
    {
        if (self::USE_TRANSACTIONS) {
            $this->em->getConnection()->beginTransaction();
        }
    }

    /**
     * Commit transaction if true.
     */
    protected function commitTransaction()
    {
        if (self::USE_TRANSACTIONS) {
            $this->em->getConnection()->commit();
        }
    }

    /**
     * Rollback transaction if true.
     */
    protected function rollbackTransaction()
    {
        if (self::USE_TRANSACTIONS) {
            $this->em->getConnection()->rollback();
        }
    }
}

<?php

namespace Buseta\BodegaBundle\Tests\Manager;


use Buseta\BodegaBundle\Form\Model\AlbaranModel;
use Buseta\BodegaBundle\Manager\AlbaranManager;
use Buseta\BodegaBundle\Tests\AbstractTestCase;

class AlbaranManagerTest extends AbstractTestCase
{

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @covers AlbaranModel::__constructor
     * @covers AlbaranModel::crear
     *
     * @uses \Doctrine\DBAL\Connection
     * @uses \Doctrine\ORM\EntityManager
     * @uses \Symfony\Bridge\Monolog\Logger
     * @uses \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function testCreate()
    {
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $entityManager->expects($this->any())
            ->method('persist');

        $logger = $this->getMockBuilder('\Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $eventDispatcher = $this->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $albaranManager = new AlbaranManager($entityManager, $logger, $eventDispatcher);
        $albaranModel = new AlbaranModel();

        $this->assertInstanceOf('Buseta\BodegaBundle\Entity\Albaran', $albaranManager->crear($albaranModel));
    }
}

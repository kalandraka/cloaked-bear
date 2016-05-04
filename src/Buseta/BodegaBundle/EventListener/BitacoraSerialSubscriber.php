<?php

namespace Buseta\BodegaBundle\EventListener;

use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Event\BitacoraSerial\BitacoraSerialEventInterface;
use Buseta\BodegaBundle\Extras\GeneradorSeriales;
use Buseta\BodegaBundle\Manager\BitacoraSerialManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BitacoraSerialSubscriber
 *
 * @package Buseta\BodegaBundle\EventListener
 */
class BitacoraSerialSubscriber implements EventSubscriberInterface
{
    const TRANSACTION_ERROR = 'Ha ocurrido un error al crear numeraciones seriadas';

    /**
     * @var BitacoraSerialManager
     */
    private $bitacoraSerialManager;

    /**
     * @var Logger
     */
    private $logger;


    /**
     * @param BitacoraSerialManager $bitacoraSerialManager
     * @param Logger                $logger
     */
    function __construct(BitacoraSerialManager $bitacoraSerialManager, Logger $logger)
    {
        $this->bitacoraSerialManager = $bitacoraSerialManager;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            BusetaBodegaEvents::BITACORA_SERIAL_REGISTER_EVENTS => 'registerEvents',
        );
    }

    public function registerEvents(BitacoraSerialEventInterface $event)
    {
        $this->createRegistry($event);
    }

    private function createRegistry(BitacoraSerialEventInterface $event)
    {
        if ($event->getError()) {
            return ;
        }

        try {
            $bitacoraSerialEvents = $event->getBitacoraSerialEvents();
            $result = $this->bitacoraSerialManager->bulkNativeCreateRegistry($bitacoraSerialEvents, $event->isFlush());
            if (!$result) {
                $event->setError(self::TRANSACTION_ERROR);
            }
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('%s. Detalles: %s', self::TRANSACTION_ERROR, $e->getMessage()));
            $event->setError(self::TRANSACTION_ERROR);
        }
    }
}

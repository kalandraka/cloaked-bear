<?php

namespace Buseta\BodegaBundle\EventListener;


use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Event\FilterPedidoCompraEvent;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PedidoCompraSubscriber
 *
 * @package Buseta\BodegaBundle\EventListener
 */
class PedidoCompraSubscriber implements EventSubscriberInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SequenceManager
     */
    private $sequenceManager;


    /**
     * PedidoCompraSubscriber Constructor
     *
     * @param Logger $logger
     * @param SequenceManager $sequenceManager
     */
    function __construct(Logger $logger, SequenceManager $sequenceManager)
    {
        $this->logger = $logger;
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            BusetaBodegaEvents::PEDIDOCOMPRA_PRE_CREATE  => 'preCreate',
            //BusetaBodegaEvents::PEDIDOCOMPRA_POST_CREATE  => 'undefinedEvent',
            BusetaBodegaEvents::PEDIDOCOMPRA_PRE_PROCESS => 'preProcess',
            //BusetaBodegaEvents::PEDIDOCOMPRA_PROCESS  => 'undefinedEvent',
            BusetaBodegaEvents::PEDIDOCOMPRA_POST_PROCESS => 'postProcess',
            BusetaBodegaEvents::PEDIDOCOMPRA_PRE_COMPLETE => 'preComplete',
            //BusetaBodegaEvents::PEDIDOCOMPRA_COMPLETE => 'undefinedEvent',
            BusetaBodegaEvents::PEDIDOCOMPRA_POST_COMPLETE => 'postComplete',
        );
    }

    /**
     * @param FilterPedidoCompraEvent $event
     *
     * @throws \Exception
     */
    public function preCreate(FilterPedidoCompraEvent $event)
    {
        $pedidoCompra = $event->getPedidoCompra();
        if ($this->sequenceManager->hasSequence('Buseta\BodegaBundle\Entity\PedidoCompra')) {
            $pedidoCompra->setNumeroDocumento($this->sequenceManager->getNextValue('registro_compra_seq'));
        }
    }

    /**
     * @param FilterPedidoCompraEvent $event
     *
     * @throws NotValidStateException
     */
    public function preProcess(FilterPedidoCompraEvent $event)
    {
        $pedidoCompra = $event->getPedidoCompra();
        if ($pedidoCompra->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterPedidoCompraEvent $event
     */
    public function postProcess(FilterPedidoCompraEvent $event)
    {

    }

    /**
     * @param FilterPedidoCompraEvent $event
     *
     * @throws NotValidStateException
     */
    public function preComplete(FilterPedidoCompraEvent $event)
    {
        $pedidoCompra = $event->getPedidoCompra();
        if ($pedidoCompra->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS) {
            throw new NotValidStateException();
        }
    }
}

<?php

namespace Buseta\BodegaBundle\EventListener;


use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Event\BitacoraBodega\BitacoraAlbaranEvent;
use Buseta\BodegaBundle\Event\FilterAlbaranEvent;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AlbaranSubscriber
 *
 * @package Buseta\BodegaBundle\EventListener
 */
class AlbaranSubscriber implements EventSubscriberInterface
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
     * AlbaranSubscriber Constructor
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
            BusetaBodegaEvents::ALBARAN_PRE_CREATE  => 'preCreate',
            //BusetaBodegaEvents::ALBARAN_POST_CREATE  => 'undefinedEvent',
            BusetaBodegaEvents::ALBARAN_PRE_PROCESS => 'preProcess',
            //BusetaBodegaEvents::ALBARAN_PROCESS  => 'undefinedEvent',
            BusetaBodegaEvents::ALBARAN_POST_PROCESS => 'postProcess',
            BusetaBodegaEvents::ALBARAN_PRE_COMPLETE => 'preComplete',
            //BusetaBodegaEvents::ALBARAN_COMPLETE => 'undefinedEvent',
            BusetaBodegaEvents::ALBARAN_POST_COMPLETE => 'postComplete',
        );
    }

    /** 
     * @param FilterAlbaranEvent $event
     *
     * @throws \Exception
     */
    public function preCreate(FilterAlbaranEvent $event)
    {
        $albaran = $event->getAlbaran();
        if ($this->sequenceManager->hasSequence('Buseta\BodegaBundle\Entity\Albaran')) {
            $albaran->setNumeroDocumento($this->sequenceManager->getNextValue('orden_entrada_seq'));
        }
    }

    /**
     * @param FilterAlbaranEvent $event
     *
     * @throws NotValidStateException
     */
    public function preProcess(FilterAlbaranEvent $event)
    {
        $albaran = $event->getAlbaran();
        if ($albaran->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterAlbaranEvent $event
     */
    public function postProcess(FilterAlbaranEvent $event)
    {

    }

    /**
     * @param FilterAlbaranEvent $event
     *
     * @throws NotValidStateException
     */
    public function preComplete(FilterAlbaranEvent $event)
    {
        $albaran = $event->getAlbaran();
        if ($albaran->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterAlbaranEvent $event
     * @param string|null $eventName
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function postComplete(
        FilterAlbaranEvent $event,
        $eventName = null,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $bitacoraEvent = new BitacoraAlbaranEvent($event->getAlbaran());
        $eventDispatcher->dispatch(BusetaBodegaEvents::BITACORA_INVENTORY_IN_OUT, $bitacoraEvent);

        if ($error = $bitacoraEvent->getError()) {
            $event->setError($error);
        }
    }
}

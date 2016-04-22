<?php

namespace Buseta\BodegaBundle\EventListener;


use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Event\BitacoraBodega\BitacoraAlbaranEvent;
use Buseta\BodegaBundle\Event\FilterNecesidadMaterialEvent;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class NecesidadMaterialSubscriber
 *
 * @package Buseta\BodegaBundle\EventListener
 */
class NecesidadMaterialSubscriber implements EventSubscriberInterface
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
     * NecesidadMaterialSubscriber Constructor
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
            BusetaBodegaEvents::NECESIDADMATERIAL_PRE_CREATE  => 'preCreate',
            //BusetaBodegaEvents::NECESIDADMATERIAL_POST_CREATE  => 'undefinedEvent',
            BusetaBodegaEvents::NECESIDADMATERIAL_PRE_PROCESS => 'preProcess',
            //BusetaBodegaEvents::NECESIDADMATERIAL_PROCESS  => 'undefinedEvent',
            BusetaBodegaEvents::NECESIDADMATERIAL_POST_PROCESS => 'postProcess',
            BusetaBodegaEvents::NECESIDADMATERIAL_PRE_COMPLETE => 'preComplete',
            //BusetaBodegaEvents::NECESIDADMATERIAL_COMPLETE => 'undefinedEvent',
            BusetaBodegaEvents::NECESIDADMATERIAL_POST_COMPLETE => 'postComplete',
        );
    }

    /**
     * @param FilterNecesidadMaterialEvent $event
     *
     * @throws \Exception
     */
    public function preCreate(FilterNecesidadMaterialEvent $event)
    {
        $necesidadMaterial = $event->getNecesidadMaterial();
        if ($this->sequenceManager->hasSequence('Buseta\BodegaBundle\Entity\NecesidadMaterial')) {
            $necesidadMaterial->setNumeroDocumento($this->sequenceManager->getNextValue('necesidad_material_seq'));
        }
    }

    /**
     * @param FilterNecesidadMaterialEvent $event
     *
     * @throws NotValidStateException
     */
    public function preProcess(FilterNecesidadMaterialEvent $event)
    {
        $necesidadMaterial = $event->getNecesidadMaterial();
        if ($necesidadMaterial->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterNecesidadMaterialEvent $event
     */
    public function postProcess(FilterNecesidadMaterialEvent $event)
    {

    }

    /**
     * @param FilterNecesidadMaterialEvent $event
     *
     * @throws NotValidStateException
     */
    public function preComplete(FilterNecesidadMaterialEvent $event)
    {
        $necesidadMaterial = $event->getNecesidadMaterial();
        if ($necesidadMaterial->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterNecesidadMaterialEvent $event
     * @param string|null $eventName
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function postComplete(
        FilterNecesidadMaterialEvent $event,
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

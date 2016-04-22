<?php

namespace Buseta\TallerBundle\EventListener;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Event\BitacoraBodega\BitacoraAlbaranEvent;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use Buseta\TallerBundle\Event\FilterOrdenTrabajoEvent;
use Buseta\TallerBundle\Event\OrdenTrabajoEvents;
use Buseta\TallerBundle\Manager\OrdenTrabajoManager;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrdenTrabajoSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Buseta\TallerBundle\Manager\OrdenTrabajoManager
     */
    private $ordenTrabajoManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SequenceManager
     */
    private $sequenceManager;
    /**
     * @param \Buseta\TallerBundle\Manager\OrdenTrabajoManager $ordenTrabajoManager
     * @param Logger $logger
     * @param SequenceManager $sequenceManager
     */
    function __construct(OrdenTrabajoManager $ordenTrabajoManager,Logger $logger, SequenceManager $sequenceManager)
    {
        $this->ordenTrabajoManager = $ordenTrabajoManager;
        $this->logger = $logger;
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
//            OrdenTrabajoEvents::PROCESAR_ORDEN         => 'crearSolicitudCompletada',
            OrdenTrabajoEvents::CAMBIAR_ESTADO_ABIERTO => 'cambioEstadoOrdenAbierto',
            OrdenTrabajoEvents::CAMBIAR_ESTADO_PROCESADO => 'cambioEstadoOrdenProcesado',
            OrdenTrabajoEvents::CAMBIAR_CANCELADO   => 'cambiarCancelado',
            OrdenTrabajoEvents::CAMBIAR_ESTADO_CERRADO   => 'cambioEstadoOrdenCerrado',
            OrdenTrabajoEvents::CAMBIAR_ESTADO_COMPLETADO  => 'cambioEstadoOrdenCompletado',
            OrdenTrabajoEvents::ORDENTRABAJO_PRE_CREATE  => 'preCreate',
            //OrdenTrabajoEvents::ORDENTRABAJO_POST_CREATE  => 'undefinedEvent',
            OrdenTrabajoEvents::ORDENTRABAJO_PRE_PROCESS => 'preProcess',
            //OrdenTrabajoEvents::ORDENTRABAJO_PROCESS  => 'undefinedEvent',
            OrdenTrabajoEvents::ORDENTRABAJO_POST_PROCESS => 'postProcess',
            OrdenTrabajoEvents::ORDENTRABAJO_PRE_COMPLETE => 'preComplete',
            //OrdenTrabajoEvents::ORDENTRABAJO_COMPLETE => 'undefinedEvent',
            //OrdenTrabajoEvents::ORDENTRABAJO_POST_COMPLETE => 'postComplete',
        );
    }

    public function cambiarCancelado(FilterOrdenTrabajoEvent $event)
    {
        $orden = $event->getOrden();
        $this->ordenTrabajoManager->cambiarCancelado($orden);
    }

    public function cambioEstadoOrdenCerrado(FilterOrdenTrabajoEvent $event)
    {
        $this->cambioEstado($event, 'CO' );
    }

    public function cambioEstadoOrdenAbierto(FilterOrdenTrabajoEvent $event)
    {
        $this->cambioEstado($event, 'BO' );
    }

    public function cambioEstadoOrdenProcesado(FilterOrdenTrabajoEvent $event)
    {
        $this->cambioEstado($event, 'PR' );
    }

    public function cambioEstadoOrdenCompletado(FilterOrdenTrabajoEvent $event)
    {
        $this->cambioEstado($event, 'CO');
    }

    public function cambioEstado(FilterOrdenTrabajoEvent $event, $estado)
    {
        $orden = $event->getOrden();
        $this->ordenTrabajoManager->cambiarEstado($orden, $estado);
    }


    /**
     * @param FilterOrdenTrabajoEvent $event
     *
     * @throws \Exception
     */
    public function preCreate(FilterOrdenTrabajoEvent $event)
    {
        $ordenTrabajo = $event->getOrden();
        if ($this->sequenceManager->hasSequence('Buseta\TallerBundle\Entity\OrdenTrabajo')) {
            $ordenTrabajo->setNumero($this->sequenceManager->getNextValue('ot_seq'));
        }
    }

    /**
     * @param FilterOrdenTrabajoEvent $event
     *
     * @throws NotValidStateException
     */
    public function preProcess(FilterOrdenTrabajoEvent $event)
    {
        $ordenTrabajo = $event->getOrden();
        if ($ordenTrabajo->getEstado() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterOrdenTrabajoEvent $event
     */
    public function postProcess(FilterOrdenTrabajoEvent $event)
    {

    }

    /**
     * @param FilterOrdenTrabajoEvent $event
     *
     * @throws NotValidStateException
     */
    public function preComplete(FilterOrdenTrabajoEvent $event)
    {
        $ordenTrabajo = $event->getOrden();
        if ($ordenTrabajo->getEstado() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS) {
            throw new NotValidStateException();
        }
    }

}

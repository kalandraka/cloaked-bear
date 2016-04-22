<?php

namespace Buseta\TallerBundle\EventListener;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use Buseta\TallerBundle\Event\FilterReporteEvent;
use Buseta\TallerBundle\Event\ReporteEvents;
use Buseta\TallerBundle\Manager\DiagnosticoManager;
use Buseta\TallerBundle\Manager\ReporteManager;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ReporteSubscriber
 * @package Buseta\TallerBundle\EventListener
 */
class ReporteSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Buseta\TallerBundle\Manager\ReporteManager
     */
    private $reporteManager;

    /**
     * @var \Buseta\TallerBundle\Manager\DiagnosticoManager
     */
    private $diagnosticoManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SequenceManager
     */
    private $sequenceManager;

    /**
     * @param \Buseta\TallerBundle\Manager\ReporteManager $reporteManager
     * @param \Buseta\TallerBundle\Manager\DiagnosticoManager $diagnosticoManager
     * @param Logger $logger
     * @param SequenceManager $sequenceManager
     */
    function __construct(ReporteManager $reporteManager, DiagnosticoManager $diagnosticoManager,Logger $logger, SequenceManager $sequenceManager)
    {
        $this->reporteManager = $reporteManager;
        $this->diagnosticoManager = $diagnosticoManager;
        $this->sequenceManager = $sequenceManager;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            ReporteEvents::PROCESAR_SOLICITUD         => 'crearDiagnostico',
            ReporteEvents::CAMBIAR_ESTADO_PENDIENTE   => 'cambioEstadoReportePendiente',
            ReporteEvents::CAMBIAR_ESTADO_COMPLETADO  => 'cambioEstadoReporteCompletado',
            ReporteEvents::CAMBIAR_ESTADO_CANCELADO  => 'cambioEstadoReporteCancelado',
            ReporteEvents::REPORTE_PRE_CREATE  => 'preCreate',
            //ReporteEvents::REPORTE_POST_CREATE  => 'undefinedEvent',
            ReporteEvents::REPORTE_PRE_PROCESS => 'preProcess',
            //ReporteEvents::REPORTE_PROCESS  => 'undefinedEvent',
            ReporteEvents::REPORTE_POST_PROCESS => 'postProcess',
            ReporteEvents::REPORTE_PRE_COMPLETE => 'preComplete',
            //ReporteEvents::REPORTE_COMPLETE => 'undefinedEvent',
            //ReporteEvents::REPORTE_POST_COMPLETE => 'postComplete',
        );
    }

    public function cambioEstadoReportePendiente(FilterReporteEvent $event)
    {
        $this->cambioEstado($event, 'PR');
    }

    public function cambioEstadoReporteCompletado(FilterReporteEvent $event)
    {
        $this->cambioEstado($event, 'CO');
    }

    public function cambioEstadoReporteCancelado(FilterReporteEvent $event)
    {
        $this->cambioEstado($event, 'CL');
    }

    public function cambioEstado(FilterReporteEvent $event, $estado)
    {
        $reporte = $event->getReporte();
        $this->reporteManager->cambiarEstado($reporte, $estado);
    }

    public function crearDiagnostico(FilterReporteEvent $event)
    {
        $reporte = $event->getReporte();
        $this->diagnosticoManager->crear($reporte);
    }

    /**
     * @param FilterReporteEvent $event
     *
     * @throws \Exception
     */
    public function preCreate(FilterReporteEvent $event)
    {
        $reporte = $event->getReporte();
        if ($this->sequenceManager->hasSequence('Buseta\TallerBundle\Entity\Reporte')) {
            $reporte->setNumero($this->sequenceManager->getNextValue('reporte_seq'));
        }
    }

    /**
     * @param FilterReporteEvent $event
     *
     * @throws NotValidStateException
     */
    public function preProcess(FilterReporteEvent $event)
    {
        $reporte = $event->getReporte();
        if ($reporte->getEstado() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterReporteEvent $event
     */
    public function postProcess(FilterReporteEvent $event)
    {

    }

    /**
     * @param FilterReporteEvent $event
     *
     * @throws NotValidStateException
     */
    public function preComplete(FilterReporteEvent $event)
    {
        $reporte = $event->getReporte();
        if ($reporte->getEstado() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS) {
            throw new NotValidStateException();
        }
    }
}

<?php

namespace Buseta\TallerBundle\EventListener;



use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use Buseta\TallerBundle\Event\FilterDiagnosticoEvent;
use Buseta\TallerBundle\Event\DiagnosticoEvents;
use Buseta\TallerBundle\Manager\DiagnosticoManager;
use Buseta\TallerBundle\Manager\OrdenTrabajoManager;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DiagnosticoSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Buseta\TallerBundle\Manager\DiagnosticoManager
     */
    private $diagnosticoManager;

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
     * @param \Buseta\TallerBundle\Manager\DiagnosticoManager $diagnosticoManager
     * @param \Buseta\TallerBundle\Manager\OrdenTrabajoManager $ordenTrabajoManager
     * @param Logger $logger
     * @param SequenceManager $sequenceManager
     */
    function __construct(DiagnosticoManager $diagnosticoManager, OrdenTrabajoManager $ordenTrabajoManager,Logger $logger, SequenceManager $sequenceManager)
    {
        $this->diagnosticoManager = $diagnosticoManager;
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

            DiagnosticoEvents::CAMBIAR_CANCELADO   => 'cambiarCancelado',
            DiagnosticoEvents::CAMBIAR_ESTADO_PR   => 'cambioEstadoDiagnosticoPendiente',
            DiagnosticoEvents::CAMBIAR_ESTADO_CO  => 'cambioEstadoDiagnosticoCompletado',
            DiagnosticoEvents::DIAGNOSTICO_PRE_CREATE  => 'preCreate',
            //DiagnosticoEvents::ORDENTRABAJO_POST_CREATE  => 'undefinedEvent',
            DiagnosticoEvents::DIAGNOSTICO_PRE_PROCESS => 'preProcess',
            //DiagnosticoEvents::ORDENTRABAJO_PROCESS  => 'undefinedEvent',
            DiagnosticoEvents::DIAGNOSTICO_POST_PROCESS => 'postProcess',
            DiagnosticoEvents::DIAGNOSTICO_PRE_COMPLETE => 'preComplete',
            //DiagnosticoEvents::DIAGNOSTICO_COMPLETE => 'undefinedEvent',
            //DiagnosticoEvents::DIAGNOSTICO_POST_COMPLETE => 'postComplete',
        );
    }



    public function cambiarCancelado(FilterDiagnosticoEvent $event)
    {
        $diagnostico = $event->getDiagnostico();
        $this->diagnosticoManager->cambiarCancelado($diagnostico);
    }

    public function cambioEstadoDiagnosticoPendiente(FilterDiagnosticoEvent $event)
    {
        $this->cambioEstado($event,'PR' );
    }

    public function cambioEstadoDiagnosticoCompletado(FilterDiagnosticoEvent $event)
    {
        $this->cambioEstado($event, 'CO');
    }

    public function cambioEstado(FilterDiagnosticoEvent $event, $estado)
    {
        $diagnostico = $event->getDiagnostico();
        $this->diagnosticoManager->cambiarEstado($diagnostico,$estado);
    }


    /**
     * @param FilterDiagnosticoEvent $event
     *
     * @throws \Exception
     */
    public function preCreate(FilterDiagnosticoEvent $event)
    {
        $diagnostico = $event->getDiagnostico();
        if ($this->sequenceManager->hasSequence('Buseta\TallerBundle\Entity\Diagnostico')) {
            $diagnostico->setNumero($this->sequenceManager->getNextValue('diagnostico_seq'));
        }
    }

    /**
     * @param FilterDiagnosticoEvent $event
     *
     * @throws NotValidStateException
     */
    public function preProcess(FilterDiagnosticoEvent $event)
    {
        $diagnostico = $event->getDiagnostico();
        if ($diagnostico->getEstado() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw new NotValidStateException();
        }
    }

    /**
     * @param FilterDiagnosticoEvent $event
     */
    public function postProcess(FilterDiagnosticoEvent $event)
    {

    }

    /**
     * @param FilterDiagnosticoEvent $event
     *
     * @throws NotValidStateException
     */
    public function preComplete(FilterDiagnosticoEvent $event)
    {
        $diagnostico = $event->getDiagnostico();
        if ($diagnostico->getEstado() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS) {
            throw new NotValidStateException();
        }
    }
}

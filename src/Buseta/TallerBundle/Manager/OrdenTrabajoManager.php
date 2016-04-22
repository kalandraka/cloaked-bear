<?php

namespace Buseta\TallerBundle\Manager;

use Buseta\TallerBundle\Event\FilterOrdenTrabajoEvent;
use Buseta\TallerBundle\Event\OrdenTrabajoEvents;
use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Buseta\TallerBundle\Form\Model\OrdenTrabajoModel;
use Doctrine\Common\Persistence\ObjectManager;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class OrdenTrabajo
 *
 * @package Buseta\TallerBundle\Manager
 */
class OrdenTrabajoManager extends AbstractTallerManager
{

    public function cambiarEstado(OrdenTrabajo $orden, $estado = 'BO')
    {
        try {
            $orden->setEstado($estado);
            //ver si necesito el persist ya que el elemento ya esta en la base de datos
            $this->em->persist($orden);
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('OrdenTrabajo.Persist: %s', $e->getMessage()));

            return false;
        }
    }

    public function cambiarCancelado(OrdenTrabajo $orden)
    {
        try {
            $cancelado = $orden->getCancelado();
            if ($cancelado == false) {
                $orden->setCancelado(true);
                $this->em->persist($orden);
                $this->em->flush();
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('OrdenTrabajo.Persist: %s', $e->getMessage()));

            return false;
        }
    }

    /**
     * @param OrdenTrabajoModel $model
     *
     * @return bool|OrdenTrabajo
     */
    public function crear(OrdenTrabajoModel $model)
    {
        $error = false;
        $ordenTrabajo = new OrdenTrabajo();
        $ordenTrabajo->setCreated ($model->getCreated());
        $ordenTrabajo->setDeleted ($model->getDeleted());
        $ordenTrabajo->setUpdated ($model->getUpdated());
        $ordenTrabajo->setEstado($model->getEstado());
        $ordenTrabajo->setNumero($model->getNumero());
        $ordenTrabajo->setObservaciones($model->getObservaciones());
        $ordenTrabajo->setFechaInicio($model->getFechaInicio());
        $ordenTrabajo->setFechaFinal($model->getFechaFinal());
        $ordenTrabajo->setDuracionDias($model->getDuracionDias());
        $ordenTrabajo->setDuracionHorasLaboradas($model->getDuracionHorasLaboradas());
        $ordenTrabajo->setKilometraje($model->getKilometraje());
        $ordenTrabajo->setDiagnosticadoPor($model->getDiagnosticadoPor());
        $ordenTrabajo->setAyudante($model->getAyudante());
        $ordenTrabajo->setRevisadoPor($model->getRevisadoPor());
        $ordenTrabajo->setRealizadaPor($model->getRealizadaPor());
        $ordenTrabajo->setAprobadoPor($model->getAprobadoPor());


        if ($model->getDiagnostico() !== null) {
            $ordenTrabajo->setDiagnostico($model->getDiagnostico());
        }
        if ($model->getAutobus() !== null) {
            $ordenTrabajo->setAutobus($model->getAutobus());
        }
        if ($model->getPrioridad() !== null) {
            $ordenTrabajo->setPrioridad($model->getPrioridad());
        }

        if (!$model->getTareasAdicionales()->isEmpty()) {
            foreach ($model->getTareasAdicionales() as $tareasAdicionale) {
                $ordenTrabajo->addTareasAdicionale($tareasAdicionale);
            }
        }


        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(OrdenTrabajoEvents::ORDENTRABAJO_PRE_CREATE)) {
                $preCreateEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $this->dispatcher->dispatch(OrdenTrabajoEvents::ORDENTRABAJO_PRE_CREATE, $preCreateEvent);

                if ($preCreateEvent->getError()) {
                    $error = $preCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->persist($ordenTrabajo);
            }

            if (!$error && $this->dispatcher->hasListeners(OrdenTrabajoEvents::ORDENTRABAJO_POST_CREATE)) {
                $postCreateEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $this->dispatcher->dispatch(OrdenTrabajoEvents::ORDENTRABAJO_POST_CREATE, $postCreateEvent);

                if ($postCreateEvent->getError()) {
                    $error = $postCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return $ordenTrabajo;
            }

            $this->logger->warning(sprintf('Orden Trabajo no fue creado debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al crear Orden Trabajo. Detalles: {message: %s, class: %s, line: %d}',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
        }

        $this->rollbackTransaction();

        return false;
    }

}

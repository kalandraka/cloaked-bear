<?php

namespace Buseta\TallerBundle\Manager;


use Buseta\TallerBundle\Entity\Reporte;
use Buseta\TallerBundle\Event\FilterReporteEvent;
use Buseta\TallerBundle\Event\ReporteEvents;
use Buseta\TallerBundle\Form\Model\ReporteModel;


class ReporteManager extends AbstractTallerManager
{
    /**
     * Cambia es estado de un reporte
     *
     * @param Reporte $reporte
     * @param string $estado
     *
     * @return boolean
     */
    public function cambiarEstado( Reporte $reporte , $estado = 'BO' )
    {
        //Cambia el estado
        $reporte->setEstado( $estado);

        try {
            $this->em->persist($reporte);
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Ha ocurrido un error al cambiar estado de Solicitud. Detalles: %s', $e->getMessage()));

            return false;
        }
    }

    /**
     * @param ReporteModel $model
     *
     * @return bool|Reporte
     */
    public function crear(ReporteModel $model)
    {
        $error = false;
        $reporte = new Reporte();
        $reporte->setNumero($model->getNumero());
        $reporte->setPrioridad($model->getPrioridad());
        $reporte->setEmailPersona($model->getEmailPersona());
        $reporte->setMedioReporte($model->getMedioReporte());
        $reporte->setEsUsuario($model->getEsUsuario());


        if ($model->getAutobus() !== null) {
            $reporte->setAutobus($model->getAutobus());
        }
        if ($model->getGrupo() !== null) {
            $reporte->setGrupo($model->getGrupo());
        }
        if ($model->getPrioridad() !== null) {
            $reporte->setPrioridad($model->getPrioridad());
        }

        if (!$model->getObservaciones()->isEmpty()) {
            foreach ($model->getObservaciones() as $observaciones) {
                $reporte->addObservacione($observaciones);
            }
        }
        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(ReporteEvents::REPORTE_PRE_CREATE)) {
                $preCreateEvent = new FilterReporteEvent($reporte);
                $this->dispatcher->dispatch(ReporteEvents::REPORTE_PRE_CREATE, $preCreateEvent);

                if ($preCreateEvent->getError()) {
                    $error = $preCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->persist($reporte);
            }

            if (!$error && $this->dispatcher->hasListeners(ReporteEvents::REPORTE_POST_CREATE)) {
                $postCreateEvent = new FilterReporteEvent($reporte);
                $this->dispatcher->dispatch(ReporteEvents::REPORTE_POST_CREATE, $postCreateEvent);

                if ($postCreateEvent->getError()) {
                    $error = $postCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return $reporte;
            }

            $this->logger->warning(sprintf('Solicitud no fue creado debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al crear Solicitud. Detalles: {message: %s, class: %s, line: %d}',
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

<?php

namespace Buseta\TallerBundle\Manager;

use Buseta\TallerBundle\Entity\Reporte;
use Buseta\TallerBundle\Entity\TareaDiagnostico;
use Buseta\TallerBundle\Entity\TareaAdicional;
use Buseta\TallerBundle\Entity\Diagnostico;
use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Buseta\TallerBundle\Event\DiagnosticoEvents;
use Buseta\TallerBundle\Event\FilterDiagnosticoEvent;
use Buseta\TallerBundle\Form\Model\DiagnosticoModel;
use Doctrine\Common\Persistence\ObjectManager;
use HatueySoft\SequenceBundle\HatueySoftSequenceBundle;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Session\Session;
use HatueySoft\SequenceBundle\Managers\SequenceManager;
use Symfony\Component\Security\Core\Util\ClassUtils;



/**
 * Class DiagnosticoManager
 * @package Buseta\TallerBundle\Manager
 */
class DiagnosticoManager extends AbstractTallerManager
{
    /**
     * @param DiagnosticoModel $model
     *
     * @return bool|Diagnostico
     */
    public function crear(DiagnosticoModel $model)
    {
        $error = false;
        $diagnostico = new Diagnostico();
        $diagnostico->setCreated ($model->getCreated());
        $diagnostico->setDeleted ($model->getDeleted());
        $diagnostico->setUpdated ($model->getUpdated());
        $diagnostico->setEstado($model->getEstado());
        $diagnostico->setNumero($model->getNumero());
        //$diagnostico->setObservaciones($model->getObservaciones());


        if ($model->getReporte() !== null) {
            $diagnostico->setReporte($model->getReporte());
        }
        if ($model->getAutobus() !== null) {
            $diagnostico->setAutobus($model->getAutobus());
        }
        if ($model->getPrioridad() !== null) {
            $diagnostico->setPrioridad($model->getPrioridad());
        }
        if (!$model->getTareaDiagnostico()->isEmpty()) {
            foreach ($model->getTareaDiagnostico() as $tareaDiagnostico) {
                $diagnostico->addTareaDiagnostico($tareaDiagnostico);
            }
        }


        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(DiagnosticoEvents::DIAGNOSTICO_PRE_CREATE)) {
                $preCreateEvent = new FilterDiagnosticoEvent($diagnostico);
                $this->dispatcher->dispatch(DiagnosticoEvents::DIAGNOSTICO_PRE_CREATE, $preCreateEvent);

                if ($preCreateEvent->getError()) {
                    $error = $preCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->persist($diagnostico);
            }

            if (!$error && $this->dispatcher->hasListeners(DiagnosticoEvents::DIAGNOSTICO_POST_CREATE)) {
                $postCreateEvent = new FilterDiagnosticoEvent($diagnostico);
                $this->dispatcher->dispatch(DiagnosticoEvents::DIAGNOSTICO_POST_CREATE, $postCreateEvent);

                if ($postCreateEvent->getError()) {
                    $error = $postCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return $diagnostico;
            }

            $this->logger->warning(sprintf('Diagnostico no fue creado debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al crear Diagnostico. Detalles: {message: %s, class: %s, line: %d}',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
        }

        $this->rollbackTransaction();

        return false;
    }

    /**
     *
     * @param diagnostico $diagnostico
     * @param string $estado
     */
    public function cambiarEstado( Diagnostico $diagnostico , $estado = 'BO' )
    {
        //Cambia el estado
        $diagnostico->setEstado($estado);

        $this->em->persist($diagnostico);
        $this->em->flush();
    }

    public function cambiarCancelado( Diagnostico $diagnostico)
    {
        try{
            $cancelado = $diagnostico->getCancelado();
            if($cancelado === false){
                $diagnostico->setCancelado(true);
                $diagnostico->setEstado('CL');

                $this->em->persist($diagnostico);
                $this->em->flush();
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Diagnostico.Persist: %s', $e->getMessage()));

            return false;
        }
    }

}

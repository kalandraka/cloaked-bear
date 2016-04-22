<?php

namespace Buseta\BodegaBundle\Manager;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Entity\NecesidadMaterial;
use Buseta\BodegaBundle\Event\FilterNecesidadMaterialEvent;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel;

/**
 * Class NecesidadMaterial
 *
 * @package Buseta\BodegaBundle\Manager
 */
class NecesidadMaterialManager extends AbstractBodegaManager
{
    /**
     * @param NecesidadMaterialModel $model
     *
     * @return bool|NecesidadMaterial
     */
    public function crear(NecesidadMaterialModel $model)
    {
        $error = false;
        $necesidadMaterial = new NecesidadMaterial();
        $necesidadMaterial->setNumeroReferencia($model->getNumeroReferencia());
        $necesidadMaterial->setObservaciones($model->getObservaciones());
        $necesidadMaterial->setFechaPedido($model->getFechaPedido());
        $necesidadMaterial->setImporteTotalLineas($model->getImporteTotalLineas());
        $necesidadMaterial->setImporteTotal($model->getImporteTotal());
        $necesidadMaterial->setImporteCompra($model->getImporteCompra());
        $necesidadMaterial->setImporteDescuento($model->getImporteDescuento());
        $necesidadMaterial->setImporteImpuesto($model->getImporteImpuesto());
        $necesidadMaterial->setTercero($model->getTercero());
        $necesidadMaterial->setAlmacen($model->getAlmacen());
        $necesidadMaterial->setFormaPago($model->getFormaPago());
        $necesidadMaterial->setCondicionesPago($model->getCondicionesPago());
        $necesidadMaterial->setMoneda($model->getMoneda());
        $necesidadMaterial->setDescuento($model->getDescuento());
        $necesidadMaterial->setImpuesto($model->getImpuesto());


        if ($model->getNumeroDocumento() !== null){
            $necesidadMaterial->setNumeroDocumento($model->getNumeroDocumento());
        }
        if ($model->getEstadoDocumento() !== null) {
            $necesidadMaterial->setEstadoDocumento($model->getEstadoDocumento());
        }
        if (!$model->getNecesidadMaterialLineas()->isEmpty()) {
            foreach ($model->getNecesidadMaterialLineas() as $lineas) {
                $necesidadMaterial->addNecesidadMaterialLinea($lineas);
            }
        }

        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(BusetaBodegaEvents::NECESIDADMATERIAL_PRE_CREATE)) {
                $preCreateEvent = new FilterNecesidadMaterialEvent($necesidadMaterial);
                $this->dispatcher->dispatch(BusetaBodegaEvents::NECESIDADMATERIAL_PRE_CREATE, $preCreateEvent);

                if ($preCreateEvent->getError()) {
                    $error = $preCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->persist($necesidadMaterial);
            }

            if (!$error && $this->dispatcher->hasListeners(BusetaBodegaEvents::NECESIDADMATERIAL_POST_CREATE)) {
                $postCreateEvent = new FilterNecesidadMaterialEvent($necesidadMaterial);
                $this->dispatcher->dispatch(BusetaBodegaEvents::NECESIDADMATERIAL_POST_CREATE, $postCreateEvent);

                if ($postCreateEvent->getError()) {
                    $error = $postCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return $necesidadMaterial;
            }

            $this->logger->warning(sprintf('Necesidad Material no fue creada debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al crear Necesidad Material. Detalles: {message: %s, class: %s, line: %d}',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
        }

        $this->rollbackTransaction();

        return false;
    }

    public function procesar(NecesidadMaterial $necesidadMaterial)
    {
        $error = false;
        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(BusetaBodegaEvents::NECESIDADMATERIAL_PRE_PROCESS)) {
                $preProcessEvent = new FilterNecesidadMaterialEvent($necesidadMaterial);
                $this->dispatcher->dispatch(BusetaBodegaEvents::NECESIDADMATERIAL_PRE_PROCESS, $preProcessEvent);

                if ($preProcessEvent->getError()) {
                    $error = $preProcessEvent->getError();
                }
            }

            if (!$error) {
                $this->cambiarEstado($necesidadMaterial, BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS, $error);
            }

            if (!$error && $this->dispatcher->hasListeners(BusetaBodegaEvents::NECESIDADMATERIAL_POST_PROCESS)) {
                $postProcessEvent = new FilterNecesidadMaterialEvent($necesidadMaterial);
                $this->dispatcher->dispatch(BusetaBodegaEvents::NECESIDADMATERIAL_POST_PROCESS, $postProcessEvent);

                if ($postProcessEvent->getError()) {
                    $error = $postProcessEvent->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return true;
            }

            $this->logger->warning(sprintf('Necesidad Material no procesada debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al completar Necesidad Material. Detalles: {message: %s, class: %s, line: %d}',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
        }

        $this->rollbackTransaction();

        return false;
    }

    private function cambiarEstado(NecesidadMaterial $necesidadMaterial, $estado, &$error)
    {
        try {
            $necesidadMaterial->setEstadoDocumento($estado);
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf('Ha ocurrido un error al cambiar estado de la Necesidad Material. Detalles: %s', $e->getMessage())
            );

            $error = 'Ha ocurrido un error al cambiar estado de la Necesidad Material.';

            return false;
        }
    }

    /**
     * Set albaran status as Completed
     *
     * @param NecesidadMaterial $necesidadMaterial
     *
     * @return bool
     */
    public function completar(NecesidadMaterial $necesidadMaterial)
    {
        $error = false;
        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(BusetaBodegaEvents::NECESIDADMATERIAL_PRE_COMPLETE)) {
                $preCompleteEvent = new FilterNecesidadMaterialEvent($necesidadMaterial);
                $this->dispatcher->dispatch(BusetaBodegaEvents::NECESIDADMATERIAL_PRE_COMPLETE, $preCompleteEvent);

                if ($preCompleteEvent->getError()) {
                    $error = $preCompleteEvent->getError();
                }
            }

            if (!$error) {
                $this->cambiarEstado($necesidadMaterial, BusetaBodegaDocumentStatus::DOCUMENT_STATUS_COMPLETE, $error);
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return true;
            }

            $this->logger->warning(sprintf('Necesidad Material no completada debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf(
                    'Ha ocurrido un error al Necesidad Material de Entrada. Detalles: {message: %s, class: %s, line: %d}',
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

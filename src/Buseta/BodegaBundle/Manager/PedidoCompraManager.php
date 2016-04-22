<?php

namespace Buseta\BodegaBundle\Manager;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\BusetaBodegaEvents;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\PedidoCompra;
use Buseta\BodegaBundle\Event\FilterPedidoCompraEvent;
use Buseta\BodegaBundle\Exceptions\NotValidStateException;
use Buseta\BodegaBundle\Form\Model\AlbaranModel;
use Buseta\BodegaBundle\Form\Model\PedidoCompraModel;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PedidoCompra
 *
 * @package Buseta\BodegaBundle\Manager
 */
class PedidoCompraManager extends AbstractBodegaManager
{
    /**
     * @param PedidoCompraModel $model
     *
     * @return bool|PedidoCompra
     */
    public function crear(PedidoCompraModel $model)
    {
        $error = false;
        $pedidoCompra = new PedidoCompra();
        $pedidoCompra->setNumeroReferencia($model->getNumeroReferencia());
        $pedidoCompra->setObservaciones($model->getObservaciones());
        $pedidoCompra->setFechaPedido($model->getFechaPedido());
        $pedidoCompra->setImporteTotalLineas($model->getImporteTotalLineas());
        $pedidoCompra->setImporteTotal($model->getImporteTotal());
        $pedidoCompra->setImporteCompra($model->getImporteCompra());
        $pedidoCompra->setImporteDescuento($model->getImporteDescuento());
        $pedidoCompra->setImporteImpuesto($model->getImporteImpuesto());
        $pedidoCompra->setTercero($model->getTercero());
        $pedidoCompra->setAlmacen($model->getAlmacen());
        $pedidoCompra->setFormaPago($model->getFormaPago());
        $pedidoCompra->setCondicionesPago($model->getCondicionesPago());
        $pedidoCompra->setMoneda($model->getMoneda());
        $pedidoCompra->setDescuento($model->getDescuento());
        $pedidoCompra->setImpuesto($model->getImpuesto());


        if ($model->getNumeroDocumento() !== null){
            $pedidoCompra->setNumeroDocumento($model->getNumeroDocumento());
        }
        if ($model->getEstadoDocumento() !== null) {
            $pedidoCompra->setEstadoDocumento($model->getEstadoDocumento());
        }
        if (!$model->getPedidoCompraLineas()->isEmpty()) {
            foreach ($model->getPedidoCompraLineas() as $lineas) {
                $pedidoCompra->addPedidoCompraLinea($lineas);
            }
        }

        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(BusetaBodegaEvents::PEDIDOCOMPRA_PRE_CREATE)) {
                $preCreateEvent = new FilterPedidoCompraEvent($pedidoCompra);
                $this->dispatcher->dispatch(BusetaBodegaEvents::PEDIDOCOMPRA_PRE_CREATE, $preCreateEvent);

                if ($preCreateEvent->getError()) {
                    $error = $preCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->persist($pedidoCompra);
            }

            if (!$error && $this->dispatcher->hasListeners(BusetaBodegaEvents::PEDIDOCOMPRA_POST_CREATE)) {
                $postCreateEvent = new FilterPedidoCompraEvent($pedidoCompra);
                $this->dispatcher->dispatch(BusetaBodegaEvents::PEDIDOCOMPRA_POST_CREATE, $postCreateEvent);

                if ($postCreateEvent->getError()) {
                    $error = $postCreateEvent->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                // Try and commit the transaction, aqui puede ocurrir un error
                $this->commitTransaction();

                return $pedidoCompra;
            }

            $this->logger->warning(sprintf('Pedido de Compra no fue creado debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al crear Pedido de Compra. Detalles: {message: %s, class: %s, line: %d}',
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
     * Process PedidoCompra instance
     *
     * @param PedidoCompra $pedidoCompra
     *
     * @return bool|string
     */
    public function procesar(PedidoCompra $pedidoCompra)
    {
        $error = false;
        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(BusetaBodegaEvents::PEDIDOCOMPRA_PRE_PROCESS)) {
                $preProcessEvent = new FilterPedidoCompraEvent($pedidoCompra);
                $this->dispatcher->dispatch(BusetaBodegaEvents::PEDIDOCOMPRA_PRE_PROCESS, $preProcessEvent);

                if ($preProcessEvent->getError()) {
                    $error = $preProcessEvent->getError();
                }
            }

            if (!$error) {
                $this->cambiarEstado($pedidoCompra, BusetaBodegaDocumentStatus::DOCUMENT_STATUS_PROCESS, $error);
            }

            if (!$error && $this->dispatcher->hasListeners(BusetaBodegaEvents::PEDIDOCOMPRA_POST_PROCESS)) {
                $postProcessEvent = new FilterPedidoCompraEvent($pedidoCompra);
                $this->dispatcher->dispatch(BusetaBodegaEvents::PEDIDOCOMPRA_POST_PROCESS, $postProcessEvent);

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

            $this->logger->warning(sprintf('Orden de Entrada no procesada debido a errores previos: %s', $error));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Ha ocurrido un error al completar Orden de Entrada. Detalles: {message: %s, class: %s, line: %d}',
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
     * Change PedidoCompra document status
     *
     * @param PedidoCompra $pedidoCompra
     * @param string $estado
     * @param boolean|string $error
     *
     * @return bool|string
     */
    private function cambiarEstado(PedidoCompra $pedidoCompra, $estado, &$error)
    {
        try {
            $pedidoCompra->setEstadoDocumento($estado);
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf('Ha ocurrido un error al cambiar estado del Registro de Compra. Detalles: %s', $e->getMessage())
            );

            $error = 'Ha ocurrido un error al cambiar estado del Registro de Compra.';

            return false;
        }
    }

    /**
     * Set PedidoCompra status as Completed
     *
     * @param PedidoCompra $pedidoCompra
     *
     * @return bool
     */
    public function completar(PedidoCompra $pedidoCompra)
    {
        $error      = false;
        try {
            $this->beginTransaction();

            if ($this->dispatcher->hasListeners(BusetaBodegaEvents::PEDIDOCOMPRA_PRE_COMPLETE)) {
                $preCompleteEvent = new FilterPedidoCompraEvent($pedidoCompra);
                $this->dispatcher->dispatch(BusetaBodegaEvents::PEDIDOCOMPRA_PRE_COMPLETE, $preCompleteEvent);

                if ($preCompleteEvent->getError()) {
                    $error = $preCompleteEvent->getError();
                }
            }

            if (!$error) {
                $this->cambiarEstado($pedidoCompra, BusetaBodegaDocumentStatus::DOCUMENT_STATUS_COMPLETE, $error);
            }

                if (!$error) {
                    $this->em->flush();

                    // Try and commit the transaction, aqui puede ocurrir un error
                    $this->commitTransaction();

                    return true;
                }


            $this->logger->warning(sprintf('Registro de Compra no completado debido a errores previos: %s', $error));

        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf(
                    'Ha ocurrido un error al completar Registro de Compra. Detalles: {message: %s, class: %s, line: %d}',
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

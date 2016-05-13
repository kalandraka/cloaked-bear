<?php

namespace Buseta\BodegaBundle\Form\Model\Converters;


use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\Interfaces\AlbaranInterface;
use Buseta\BodegaBundle\Entity\Interfaces\PedidoCompraInterface;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Form\Model\AlbaranModel;

class AlbaranConverter
{
    static public function getModel(AlbaranInterface $object)
    {
        $model = new AlbaranModel();

        $model->setId($object->getId());
        $model->setNumeroReferencia($object->getNumeroReferencia());
        $model->setNumeroDocumento($object->getNumeroDocumento());
        $model->setTercero($object->getTercero());
        $model->setBodega($object->getBodega());
        $model->setFechaMovimiento($object->getFechaMovimiento());
        $model->setFechaContable($object->getFechaContable());

        return $model;
    }

    static public function getEntity(AlbaranInterface $model, Albaran &$albaran = null)
    {
        $update = $albaran !== null;
        $albaran !== null ?: $albaran = new Albaran();

        if (!$update && $model->getNumeroDocumento() !== null) {
            $albaran->setNumeroDocumento($model->getNumeroDocumento());
        }
        $albaran->setNumeroReferencia($model->getNumeroReferencia());
        $albaran->setTercero($model->getTercero());
        $albaran->setBodega($model->getBodega());
        $albaran->setFechaContable($model->getFechaContable());
        $albaran->setFechaMovimiento($model->getFechaMovimiento());

        return $albaran;
    }

    static public function convertFrom(PedidoCompraInterface $pedidoCompra)
    {
        $albaran = new Albaran();

        $albaran->setTercero($pedidoCompra->getTercero());
        $albaran->setBodega($pedidoCompra->getBodega());
        $albaran->setPedidoCompra($pedidoCompra);

        if ($pedidoCompra->getPedidoCompraLineas()->count() > 0) {
            foreach ($pedidoCompra->getPedidoCompraLineas()->getIterator() as $linea) {
                /** @var PedidoCompraLinea $linea */
                $albaranLinea = new AlbaranLinea();
                $albaranLinea->setProducto($linea->getProducto());
                $albaranLinea->setBodega($pedidoCompra->getBodega());
                $albaranLinea->setCantidadMovida($linea->getCantidadPedido());
                $albaranLinea->setUom($linea->getUom());

                $albaran->addAlbaranLinea($albaranLinea);
            }
        }

        return $albaran;
    }
}

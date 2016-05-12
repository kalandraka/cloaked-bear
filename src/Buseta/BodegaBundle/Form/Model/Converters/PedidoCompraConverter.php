<?php

namespace Buseta\BodegaBundle\Form\Model\Converters;

use Buseta\BodegaBundle\Entity\Interfaces\NecesidadMaterialInterface;
use Buseta\BodegaBundle\Entity\Interfaces\PedidoCompraInterface;
use Buseta\BodegaBundle\Entity\PedidoCompra;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Form\Model\PedidoCompraModel;

final class PedidoCompraConverter
{
    /**
     * @param PedidoCompra $object
     *
     * @return PedidoCompraModel
     */
    static public function getModel(PedidoCompraInterface $object)
    {
        $model = new PedidoCompraModel();

        $model->setId($object->getId());
        $model->setNumeroReferencia($object->getNumeroReferencia());
        $model->setNumeroDocumento($object->getNumeroDocumento());
        $model->setEstadoDocumento($object->getEstadoDocumento());
        $model->setFechaPedido($object->getFechaPedido());
        $model->setImpuesto($object->getImpuesto());
        $model->setImporteImpuesto($object->getImporteImpuesto());
        $model->setDescuento($object->getDescuento());
        $model->setImporteDescuento($object->getImporteDescuento());
        $model->setImporteCompra($object->getImporteCompra());
        $model->setImporteTotal($object->getImporteTotal());
        $model->setImporteTotalLineas($object->getImporteTotalLineas());
        $model->setObservaciones($object->getObservaciones());

        if ($object->getTercero()) {
            $model->setTercero($object->getTercero());
        }
        if ($object->getBodega()) {
            $model->setBodega($object->getBodega());
        }
        if ($object->getMoneda()) {
            $model->setMoneda($object->getMoneda());
        }
        if ($object->getFormaPago()) {
            $model->setFormaPago($object->getFormaPago());
        }
        if ($object->getCondicionesPago()) {
            $model->setCondicionesPago($object->getCondicionesPago());
        }
//        if ($object->getPedidoCompraLineas()->count() > 0) {
//            foreach ($object->getPedidoCompraLineas()->getIterator() as $linea) {
//                $model->addPedidoCompraLinea($linea);
//            }
//        }

        return $model;
    }

    /**
     * @param PedidoCompraInterface $model
     * @param PedidoCompra          $pedidoCompra
     *
     * @return PedidoCompra
     */
    static public function getEntity(PedidoCompraInterface $model, PedidoCompra &$pedidoCompra = null)
    {
        $pedidoCompra !== null ?: $pedidoCompra = new PedidoCompra();

        if ($model->getNumeroDocumento() !== null){
            $pedidoCompra->setNumeroDocumento($model->getNumeroDocumento());
        }
        $pedidoCompra->setNumeroReferencia($model->getNumeroReferencia());
        if ($model->getEstadoDocumento() !== null) {
            $pedidoCompra->setEstadoDocumento($model->getEstadoDocumento());
        }
        $pedidoCompra->setObservaciones($model->getObservaciones());
        $pedidoCompra->setFechaPedido($model->getFechaPedido());
        $pedidoCompra->setImporteTotalLineas($model->getImporteTotalLineas());
        $pedidoCompra->setImporteTotal($model->getImporteTotal());
        $pedidoCompra->setImporteCompra($model->getImporteCompra());
        $pedidoCompra->setImporteDescuento($model->getImporteDescuento());
        $pedidoCompra->setImporteImpuesto($model->getImporteImpuesto());
        $pedidoCompra->setTercero($model->getTercero());
        $pedidoCompra->setBodega($model->getBodega());
        $pedidoCompra->setFormaPago($model->getFormaPago());
        $pedidoCompra->setCondicionesPago($model->getCondicionesPago());
        $pedidoCompra->setMoneda($model->getMoneda());
        $pedidoCompra->setDescuento($model->getDescuento());
        $pedidoCompra->setImpuesto($model->getImpuesto());

//        if ($model->getPedidoCompraLineas()->count() > 0) {
//            foreach ($model->getPedidoCompraLineas() as $lineas) {
//                $pedidoCompra->addPedidoCompraLinea($lineas);
//            }
//        }

        return $pedidoCompra;
    }

    /**
     * @param NecesidadMaterialInterface $necesidadMaterial
     *
     * @return PedidoCompra
     */
    static public function convertFrom(NecesidadMaterialInterface $necesidadMaterial)
    {
        $pedidoCompra = new PedidoCompra();

        $pedidoCompra->setTercero($necesidadMaterial->getTercero());
        $pedidoCompra->setBodega($necesidadMaterial->getBodega());
        $pedidoCompra->setFechaPedido($necesidadMaterial->getFechaPedido());
        $pedidoCompra->setMoneda($necesidadMaterial->getMoneda());
        $pedidoCompra->setFormaPago($necesidadMaterial->getFormaPago());
        $pedidoCompra->setCondicionesPago($necesidadMaterial->getCondicionesPago());
        $pedidoCompra->setImpuesto($necesidadMaterial->getImpuesto());
        $pedidoCompra->setDescuento($necesidadMaterial->getDescuento());
        $pedidoCompra->setImporteDescuento($necesidadMaterial->getImporteDescuento());
        $pedidoCompra->setImporteImpuesto($necesidadMaterial->getImporteImpuesto());
        $pedidoCompra->setImporteTotalLineas($necesidadMaterial->getImporteTotalLineas());
        $pedidoCompra->setImporteCompra($necesidadMaterial->getImporteCompra());
        $pedidoCompra->setImporteTotal($necesidadMaterial->getImporteTotal());
        $pedidoCompra->setNecesidadMaterial($necesidadMaterial);

        if ($necesidadMaterial->getNecesidadMaterialLineas()->count() > 0) {
            foreach ($necesidadMaterial->getNecesidadMaterialLineas()->getIterator() as $linea) {
                /** @var \Buseta\BodegaBundle\Entity\NecesidadMaterialLinea $linea */
                $pedidoCompraLinea = new PedidoCompraLinea();
                $pedidoCompraLinea->setProducto($linea->getProducto());
                $pedidoCompraLinea->setCantidadPedido($linea->getCantidadPedido());
                $pedidoCompraLinea->setUom($linea->getUom());
                $pedidoCompraLinea->setPrecioUnitario($linea->getPrecioUnitario());
                $pedidoCompraLinea->setImpuesto($linea->getImpuesto());
                $pedidoCompraLinea->setPorcientoDescuento($linea->getPorcientoDescuento());
                $pedidoCompraLinea->setImporteLinea($linea->getImporteLinea());

                $pedidoCompra->addPedidoCompraLinea($pedidoCompraLinea);
            }
        }

        return $pedidoCompra;
    }
}

<?php

namespace Buseta\BodegaBundle\Form\Model\Converters;


use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Entity\Interfaces\NecesidadMaterialInterface;
use Buseta\BodegaBundle\Entity\NecesidadMaterial;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel;

class NecesidadMaterialConverter
{
    static public function getModel(NecesidadMaterialInterface $object)
    {
        if (!($object instanceof NecesidadMaterial)) {
            throw new \Exception('$object debe ser de tipo Buseta\BodegaBundle\Entity\NecesidadMaterial');
        }

        $model = new NecesidadMaterialModel();
        $model->setId($object->getId());
        $model->setEstadoDocumento($object->getEstadoDocumento());
        $model->setFechaPedido($object->getFechaPedido());
        $model->setImporteCompra($object->getImporteCompra());
        $model->setImporteTotal($object->getImporteTotal());
        $model->setImporteTotalLineas($object->getImporteTotalLineas());
        $model->setNumeroDocumento($object->getNumeroDocumento());
        $model->setNumeroReferencia($object->getNumeroReferencia());
        $model->setDescuento($object->getDescuento());
        $model->setImpuesto($object->getImpuesto());
        $model->setImporteDescuento($object->getImporteDescuento());
        $model->setImporteImpuesto($object->getImporteImpuesto());
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
        // This implementation is deprecated, because Necesidad Material Lineas is managed in other workflow
//        if ($object->getNecesidadMaterialLineas()->count() > 0) {
//            $model->setNecesidadMaterialLineas($object->getNecesidadMaterialLineas());
//        }

        return $model;
    }

    static public function getEntity(NecesidadMaterialInterface $model, NecesidadMaterial &$necesidadMaterial = null)
    {
        $necesidadMaterial !== null ?: $necesidadMaterial = new NecesidadMaterial();

        if ($model->getEstadoDocumento() === null && $necesidadMaterial->getEstadoDocumento() === null) {
            $necesidadMaterial->setEstadoDocumento(BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT);
        } else {
            $necesidadMaterial->setEstadoDocumento($model->getEstadoDocumento());
        }

        $necesidadMaterial->setNumeroDocumento($model->getNumeroDocumento());
        $necesidadMaterial->setNumeroReferencia($model->getNumeroReferencia());
        $necesidadMaterial->setFechaPedido($model->getFechaPedido());
        $necesidadMaterial->setImporteCompra($model->getImporteCompra());
        $necesidadMaterial->setImporteTotal($model->getImporteTotal());
        $necesidadMaterial->setImporteTotalLineas($model->getImporteTotalLineas());
        $necesidadMaterial->setDescuento($model->getDescuento());
        $necesidadMaterial->setImpuesto($model->getImpuesto());
        $necesidadMaterial->setImporteDescuento($model->getImporteDescuento());
        $necesidadMaterial->setImporteImpuesto($model->getImporteImpuesto());
        $necesidadMaterial->setObservaciones($model->getObservaciones());

        if ($model->getTercero()) {
            $necesidadMaterial->setTercero($model->getTercero());
        }
        if ($model->getBodega()) {
            $necesidadMaterial->setBodega($model->getBodega());
        }
        if ($model->getMoneda()) {
            $necesidadMaterial->setMoneda($model->getMoneda());
        }
        if ($model->getFormaPago()) {
            $necesidadMaterial->setFormaPago($model->getFormaPago());
        }
        if ($model->getCondicionesPago()) {
            $necesidadMaterial->setCondicionesPago($model->getCondicionesPago());
        }

        return $necesidadMaterial;
    }
}

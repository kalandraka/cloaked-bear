<?php

namespace Buseta\BodegaBundle\Form\Model;

use Buseta\BodegaBundle\Entity\NecesidadMaterial;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Date;

/**
 * NecesidadMaterial Model
 *
 */
class NecesidadMaterialModel
{
    /**
     * @var integer
     *
     */
    private $id;

    /**
     * @var string
     */
    private $numero_documento;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $numero_referencia;

    /**
     * @var string
     */
    private $observaciones;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     * @Assert\NotBlank()
     */
    private $tercero;

    /**
     * @var date
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $fecha_pedido;

    /**
     * @var \Buseta\BodegaBundle\Entity\Bodega
     * @Assert\NotBlank()
     */
    private $almacen;

    /**
     * @var \Buseta\NomencladorBundle\Entity\Moneda
     * @Assert\NotBlank()
     */
    private $moneda;

    /**
     * @var \Buseta\NomencladorBundle\Entity\FormaPago
     * @Assert\NotBlank()
     */
    private $forma_pago;

    /**
     * @var \Buseta\TallerBundle\Entity\CondicionesPago
     * @Assert\NotBlank()
     */
    private $condiciones_pago;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $estado_documento = 'BO';

    /**
     * @var float
     */
    private $descuento;

    /**
     * @var \Buseta\TallerBundle\Entity\Impuesto
     */
    private $impuesto;

    /**
     * @var float
     */
    private $importeCompra;

    /**
     * @var float
     */
    private $importe_total_lineas;

    /**
     * @var float
     */
    private $importeDescuento;

    /**
     * @var float
     */
    private $importeImpuesto;

    /**
     * @var float
     */
    private $importe_total;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Assert\NotBlank()
     */
    private $necesidad_material_lineas;

    /**
     * @var \DateTime
     *
     */
    private $created;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     */
    private $createdby;

    /**
     * @var \DateTime
     *
     */
    private $updated;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     */
    private $updatedby;

    /**
     * @var boolean
     *
     */
    private $deleted;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     */
    private $deletedby;

    /**
     * Constructor
     */
    public function __construct(NecesidadMaterial $necesidadmaterial = null)
    {
        $this->necesidad_material_lineas = new \Doctrine\Common\Collections\ArrayCollection();

        if ($necesidadmaterial !== null) {
            $this->id = $necesidadmaterial->getId();
            $this->created = $necesidadmaterial->getCreated();
            $this->createdby = $necesidadmaterial->getCreatedby();
            $this->deleted = $necesidadmaterial->getDeleted();
            $this->deletedby = $necesidadmaterial->getDeletedby();
            $this->updated = $necesidadmaterial->getUpdated();
            $this->updatedby = $necesidadmaterial->getUpdatedby();
            $this->estado_documento = $necesidadmaterial->getEstadoDocumento();
            $this->fecha_pedido = $necesidadmaterial->getFechaPedido();
            $this->importeCompra = $necesidadmaterial->getImporteCompra();
            $this->importe_total = $necesidadmaterial->getImporteTotal();
            $this->importe_total_lineas = $necesidadmaterial->getImporteTotalLineas();
            $this->numero_documento = $necesidadmaterial->getNumeroDocumento();
            $this->numero_referencia = $necesidadmaterial->getNumeroReferencia();
            $this->descuento        = $necesidadmaterial->getDescuento();
            $this->impuesto         = $necesidadmaterial->getImpuesto();
            $this->importeDescuento = $necesidadmaterial->getImporteDescuento();
            $this->importeImpuesto  = $necesidadmaterial->getImporteImpuesto();
            $this->observaciones = $necesidadmaterial->getObservaciones();

            if ($necesidadmaterial->getTercero()) {
                $this->tercero  = $necesidadmaterial->getTercero();
            }
            if ($necesidadmaterial->getAlmacen()) {
                $this->almacen  = $necesidadmaterial->getAlmacen();
            }
            if ($necesidadmaterial->getMoneda()) {
                $this->moneda  = $necesidadmaterial->getMoneda();
            }
            if ($necesidadmaterial->getFormaPago()) {
                $this->forma_pago  = $necesidadmaterial->getFormaPago();
            }
            if ($necesidadmaterial->getCondicionesPago()) {
                $this->condiciones_pago  = $necesidadmaterial->getCondicionesPago();
            }
            if (!$necesidadmaterial->getNecesidadMaterialLineas()->isEmpty()) {
                $this->necesidad_material_lineas = $necesidadmaterial->getNecesidadMaterialLineas();
            } else {
                $this->necesidad_material_lineas = new ArrayCollection();
            }
        }
    }

    /**
     * @return NecesidadMaterial
     */
    public function getEntityData()
    {
        $necesidadmaterial = new NecesidadMaterial();
        $necesidadmaterial->setCreated($this->getCreated());
        $necesidadmaterial->setCreatedby($this->getCreatedby());
        $necesidadmaterial->setDeleted($this->getDeleted());
        $necesidadmaterial->setDeletedby($this->getDeletedby());
        $necesidadmaterial->setEstadoDocumento($this->getEstadoDocumento());
        $necesidadmaterial->setFechaPedido($this->getFechaPedido());
        $necesidadmaterial->setImporteCompra($this->getImporteCompra());
        $necesidadmaterial->setImporteTotal($this->getImporteTotal());
        $necesidadmaterial->setImporteTotalLineas($this->getImporteTotalLineas());
        $necesidadmaterial->setNumeroDocumento($this->getNumeroDocumento());
        $necesidadmaterial->setNumeroReferencia($this->getNumeroReferencia());
        $necesidadmaterial->setUpdated($this->getUpdated());
        $necesidadmaterial->setUpdatedby($this->getUpdatedby());
        $necesidadmaterial->setImpuesto($this->getImpuesto());
        $necesidadmaterial->setDescuento($this->getDescuento());
        $necesidadmaterial->setImporteImpuesto($this->getImporteImpuesto());
        $necesidadmaterial->setImporteDescuento($this->getImporteDescuento());
        $necesidadmaterial->setObservaciones($this->getObservaciones());

        if ($this->getTercero() !== null) {
            $necesidadmaterial->setTercero($this->getTercero());
        }

        if ($this->getAlmacen() !== null) {
            $necesidadmaterial->setAlmacen($this->getAlmacen());
        }

        if ($this->getMoneda() !== null) {
            $necesidadmaterial->setMoneda($this->getMoneda());
        }

        if ($this->getFormaPago() !== null) {
            $necesidadmaterial->setFormaPago($this->getFormaPago());
        }

        if ($this->getCondicionesPago() !== null) {
            $necesidadmaterial->setCondicionesPago($this->getCondicionesPago());
        }

        if (!$this->getNecesidadMaterialLineas()->isEmpty()) {
            foreach ($this->getNecesidadMaterialLineas() as $lineas) {
                $necesidadmaterial->addNecesidadMaterialLinea($lineas);
            }
        }

        return $necesidadmaterial;
    }

    /**
     * @return ArrayCollection
     */
    public function getNecesidadMaterialLineas()
    {
        return $this->necesidad_material_lineas;
    }

    /**
     * @param ArrayCollection $necesidad_material_lineas
     */
    public function setNecesidadMaterialLineas($necesidad_material_lineas)
    {
        $this->necesidad_material_lineas = $necesidad_material_lineas;
    }

    /**
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param string $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    /**
     * @return mixed
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * @param mixed $almacen
     */
    public function setAlmacen($almacen)
    {
        $this->almacen = $almacen;
    }

    /**
     * @return \Buseta\TallerBundle\Entity\CondicionesPago
     */
    public function getCondicionesPago()
    {
        return $this->condiciones_pago;
    }

    /**
     * @param \Buseta\TallerBundle\Entity\CondicionesPago $condiciones_pago
     */
    public function setCondicionesPago($condiciones_pago)
    {
        $this->condiciones_pago = $condiciones_pago;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * @param \HatueySoft\SecurityBundle\Entity\User $createdby
     */
    public function setCreatedby($createdby)
    {
        $this->createdby = $createdby;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDeletedby()
    {
        return $this->deletedby;
    }

    /**
     * @param mixed $deletedby
     */
    public function setDeletedby($deletedby)
    {
        $this->deletedby = $deletedby;
    }

    /**
     * @return mixed
     */
    public function getEstadoDocumento()
    {
        return $this->estado_documento;
    }

    /**
     * @param mixed $estado_documento
     */
    public function setEstadoDocumento($estado_documento)
    {
        $this->estado_documento = $estado_documento;
    }

    /**
     * @return mixed
     */
    public function getFechaPedido()
    {
        return $this->fecha_pedido;
    }

    /**
     * @param mixed $fecha_pedido
     */
    public function setFechaPedido($fecha_pedido)
    {
        $this->fecha_pedido = $fecha_pedido;
    }

    /**
     * @return \Buseta\NomencladorBundle\Entity\FormaPago
     */
    public function getFormaPago()
    {
        return $this->forma_pago;
    }

    /**
     * @param \Buseta\NomencladorBundle\Entity\FormaPago $forma_pago
     */
    public function setFormaPago($forma_pago)
    {
        $this->forma_pago = $forma_pago;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getImporteTotal()
    {
        return $this->importe_total;
    }

    /**
     * @param mixed $importe_total
     */
    public function setImporteTotal($importe_total)
    {
        $this->importe_total = $importe_total;
    }

    /**
     * @return mixed
     */
    public function getImporteTotalLineas()
    {
        return $this->importe_total_lineas;
    }

    /**
     * @param mixed $importe_total_lineas
     */
    public function setImporteTotalLineas($importe_total_lineas)
    {
        $this->importe_total_lineas = $importe_total_lineas;
    }

    /**
     * @return \Buseta\NomencladorBundle\Entity\Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * @param \Buseta\NomencladorBundle\Entity\Moneda $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    /**
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numero_documento;
    }

    /**
     * @param string $numero_documento
     */
    public function setNumeroDocumento($numero_documento)
    {
        $this->numero_documento = $numero_documento;
    }

    /**
     * @return string
     */
    public function getNumeroReferencia()
    {
        return $this->numero_referencia;
    }

    /**
     * @param string $numero_referencia
     */
    public function setNumeroReferencia($numero_referencia)
    {
        $this->numero_referencia = $numero_referencia;
    }

    /**
     * @return mixed
     */
    public function getTercero()
    {
        return $this->tercero;
    }

    /**
     * @param mixed $tercero
     */
    public function setTercero($tercero)
    {
        $this->tercero = $tercero;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getUpdatedby()
    {
        return $this->updatedby;
    }

    /**
     * @param mixed $updatedby
     */
    public function setUpdatedby($updatedby)
    {
        $this->updatedby = $updatedby;
    }

    /**
     * @return float
     */
    public function getImporteCompra()
    {
        return $this->importeCompra;
    }

    /**
     * @param float $importeCompra
     */
    public function setImporteCompra($importeCompra)
    {
        $this->importeCompra = $importeCompra;
    }

    /**
     * @return float
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * @param float $descuento
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    }

    /**
     * @return \Buseta\TallerBundle\Entity\Impuesto
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * @param \Buseta\TallerBundle\Entity\Impuesto $impuesto
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;
    }

    /**
     * @return float
     */
    public function getImporteDescuento()
    {
        return $this->importeDescuento;
    }

    /**
     * @param float $importeDescuento
     */
    public function setImporteDescuento($importeDescuento)
    {
        $this->importeDescuento = $importeDescuento;
    }

    /**
     * @return float
     */
    public function getImporteImpuesto()
    {
        return $this->importeImpuesto;
    }

    /**
     * @param float $importeImpuesto
     */
    public function setImporteImpuesto($importeImpuesto)
    {
        $this->importeImpuesto = $importeImpuesto;
    }
}

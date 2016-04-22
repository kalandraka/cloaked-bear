<?php

namespace Buseta\BodegaBundle\Entity;

use Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * NecesidadMaterial.
 *
 * @ORM\Table(name="d_necesidad_material")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\NecesidadMaterialRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class NecesidadMaterial
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string")
     */
    private $numero_documento;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=32)
     * @Assert\NotBlank()
     */
    private $numero_referencia;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Tercero", inversedBy="necesidadMaterial")
     */
    private $tercero;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", nullable=true)
     */
    private $observaciones;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pedido", type="date")
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $fecha_pedido;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Bodega", inversedBy="necesidadMaterial")
     */
    private $almacen;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Moneda")
     */
    private $moneda;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\FormaPago")
     */
    private $forma_pago;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\TallerBundle\Entity\CondicionesPago")
     */
    private $condiciones_pago;

    /**
     * @var string
     *
     * @ORM\Column(name="estado_documento", type="string")
     * @Assert\NotBlank()
     */
    private $estado_documento = 'BO';

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="decimal", scale=2, nullable=true)
     */
    private $descuento;

    /**
     * @var \Buseta\TallerBundle\Entity\Impuesto
     *
     * @ORM\ManyToOne(targetEntity="Buseta\TallerBundle\Entity\Impuesto")
     */
    private $impuesto;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_compra", type="decimal", scale=2, nullable=true)
     */
    private $importeCompra;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_total_lineas", type="decimal", scale=2, nullable=true)
     */
    private $importe_total_lineas;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_descuento", type="decimal", scale=2, nullable=true)
     */
    private $importeDescuento;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_impuesto", type="decimal", scale=2, nullable=true)
     */
    private $importeImpuesto;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_total", type="decimal", scale=2, nullable=true)
     */
    private $importe_total;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\NecesidadMaterial")
     */
    private $necesidadMaterial;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Buseta\BodegaBundle\Entity\NecesidadMaterialLinea", mappedBy="necesidadMaterial", cascade={"all"})
     */
    private $necesidad_material_lineas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="HatueySoft\SecurityBundle\Entity\User")
     */
    private $createdby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="HatueySoft\SecurityBundle\Entity\User")
     */
    private $updatedby;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="HatueySoft\SecurityBundle\Entity\User")
     */
    private $deletedby;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->importe_total = 0;
        $this->importe_total_lineas = 0;
        $this->necesidad_material_lineas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deleted = false;
    }

    /**
     * @param NecesidadMaterialModel $model
     * @return NecesidadMaterial
     */
    public function setModelData(NecesidadMaterialModel $model)
    {
        $this->created = $model->getCreated();
        $this->createdby = $model->getCreatedby();
        $this->numero_referencia = $model->getNumeroReferencia();
        $this->deleted = $model->getDeleted();
        $this->deletedby = $model->getDeletedby();
        $this->updated = $model->getUpdated();
        $this->updatedby = $model->getUpdatedby();
        $this->estado_documento = $model->getEstadoDocumento();
        $this->fecha_pedido = $model->getFechaPedido();
        $this->observaciones = $model->getObservaciones();
        $this->importeCompra = $model->getImporteCompra();
        $this->numero_documento = $model->getNumeroDocumento();
        $this->descuento        = $model->getDescuento();
        $this->impuesto         = $model->getImpuesto();
        $this->importeDescuento = $model->getImporteDescuento();
        $this->importeImpuesto  = $model->getImporteImpuesto();

        if ($model->getTercero()) {
            $this->tercero  = $model->getTercero();
        }
        if ($model->getAlmacen()) {
            $this->almacen  = $model->getAlmacen();
        }
        if ($model->getMoneda()) {
            $this->moneda  = $model->getMoneda();
        }
        if ($model->getFormaPago()) {
            $this->forma_pago  = $model->getFormaPago();
        }
        if ($model->getCondicionesPago()) {
            $this->condiciones_pago  = $model->getCondicionesPago();
        }

        return $this;
    }

    /**
     * @return bool
     *
     * @Assert\IsTrue(groups={"on_complete"}, message="El importe entrado para la Compra y el importe total del Registro no coinciden.")
     */
    public function isValidToComplete()
    {
        return $this->importeCompra === $this->importe_total;
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set necesidadMaterial.
     *
     * @param \Buseta\BodegaBundle\Entity\NecesidadMaterial $necesidadMaterial
     *
     * @return NecesidadMaterial
     */
    public function setNecesidadMaterial(\Buseta\BodegaBundle\Entity\NecesidadMaterial $necesidadMaterial = null)
    {
        $this->necesidadMaterial = $necesidadMaterial;

        return $this;
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
     * Get necesidadMaterial.
     *
     * @return \Buseta\BodegaBundle\Entity\NecesidadMaterial
     */
    public function getNecesidadMaterial()
    {
        return $this->necesidadMaterial;
    }

    /**
     * Set numero_documento.
     *
     * @param string $numeroDocumento
     *
     * @return NecesidadMaterial
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numero_documento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numero_documento.
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numero_documento;
    }

    /**
     * Set numero_referencia.
     *
     * @param string $numeroReferencia
     *
     * @return NecesidadMaterial
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numero_referencia = $numeroReferencia;

        return $this;
    }

    /**
     * Get numero_referencia.
     *
     * @return string
     */
    public function getNumeroReferencia()
    {
        return $this->numero_referencia;
    }

     /**
     * Set fecha_pedido.
     *
     * @param \DateTime $fechaPedido
     *
     * @return NecesidadMaterial
     */
    public function setFechaPedido($fechaPedido)
    {
        $this->fecha_pedido = $fechaPedido;

        return $this;
    }

    /**
     * Get fecha_pedido.
     *
     * @return \DateTime
     */
    public function getFechaPedido()
    {
        return $this->fecha_pedido;
    }

    /**
     * Set estado_documento.
     *
     * @param string $estadoDocumento
     *
     * @return NecesidadMaterial
     */
    public function setEstadoDocumento($estadoDocumento)
    {
        $this->estado_documento = $estadoDocumento;

        return $this;
    }

    /**
     * Get estado_documento.
     *
     * @return string
     */
    public function getEstadoDocumento()
    {
        return $this->estado_documento;
    }

    /**
     * Set importe_total_lineas.
     *
     * @param string $importeTotalLineas
     *
     * @return NecesidadMaterial
     */
    public function setImporteTotalLineas($importeTotalLineas)
    {
        $this->importe_total_lineas = $importeTotalLineas;

        return $this;
    }

    /**
     * Get importe_total_lineas.
     *
     * @return string
     */
    public function getImporteTotalLineas()
    {
        return $this->importe_total_lineas;
    }

    /**
     * Set importe_total.
     *
     * @param float $importeTotal
     *
     * @return NecesidadMaterial
     */
    public function setImporteTotal($importeTotal)
    {
        $this->importe_total = $importeTotal;

        return $this;
    }

    /**
     * Get importe_total.
     *
     * @return float
     */
    public function getImporteTotal()
    {
        return $this->importe_total;
    }

    /**
     * Get importeCompra.
     *
     * @return float
     */
    public function getImporteCompra()
    {
        return $this->importeCompra;
    }

    /**
     * Set importeCompra.
     *
     * @param float $importeCompra
     *
     * @return $this
     */
    public function setImporteCompra($importeCompra)
    {
        $this->importeCompra = $importeCompra;

        return $this;
    }

    /**
     * Set tercero.
     *
     * @param \Buseta\BodegaBundle\Entity\Tercero $tercero
     *
     * @return NecesidadMaterial
     */
    public function setTercero(\Buseta\BodegaBundle\Entity\Tercero $tercero = null)
    {
        $this->tercero = $tercero;

        return $this;
    }

    /**
     * Get tercero.
     *
     * @return \Buseta\BodegaBundle\Entity\Tercero
     */
    public function getTercero()
    {
        return $this->tercero;
    }

    /**
     * Set almacen.
     *
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacen
     *
     * @return NecesidadMaterial
     */
    public function setAlmacen(\Buseta\BodegaBundle\Entity\Bodega $almacen = null)
    {
        $this->almacen = $almacen;

        return $this;
    }

    /**
     * Get almacen.
     *
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set forma_pago.
     *
     * @param \Buseta\NomencladorBundle\Entity\FormaPago $formaPago
     *
     * @return NecesidadMaterial
     */
    public function setFormaPago(\Buseta\NomencladorBundle\Entity\FormaPago $formaPago = null)
    {
        $this->forma_pago = $formaPago;

        return $this;
    }

    /**
     * Get forma_pago.
     *
     * @return \Buseta\NomencladorBundle\Entity\FormaPago
     */
    public function getFormaPago()
    {
        return $this->forma_pago;
    }

    /**
     * Add necesidad_material_lineas.
     *
     * @param \Buseta\BodegaBundle\Entity\NecesidadMaterialLinea $necesidadMaterialLineas
     *
     * @return NecesidadMaterial
     */
    public function addNecesidadMaterialLinea(\Buseta\BodegaBundle\Entity\NecesidadMaterialLinea $necesidadMaterialLineas)
    {
        $necesidadMaterialLineas->setNecesidadMaterial($this);

        $this->necesidad_material_lineas[] = $necesidadMaterialLineas;

        return $this;
    }

    /**
     * Remove necesidad_material_lineas.
     *
     * @param \Buseta\BodegaBundle\Entity\NecesidadMaterialLinea $necesidadMaterialLineas
     */
    public function removeNecesidadMaterialLinea(\Buseta\BodegaBundle\Entity\NecesidadMaterialLinea $necesidadMaterialLineas)
    {
        $this->necesidad_material_lineas->removeElement($necesidadMaterialLineas);
    }

    /**
     * Get necesidad_material_lineas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNecesidadMaterialLineas()
    {
        return $this->necesidad_material_lineas;
    }

    /**
     * Set condiciones_pago.
     *
     * @param \Buseta\TallerBundle\Entity\CondicionesPago $condicionesPago
     *
     * @return NecesidadMaterial
     */
    public function setCondicionesPago(\Buseta\TallerBundle\Entity\CondicionesPago $condicionesPago = null)
    {
        $this->condiciones_pago = $condicionesPago;

        return $this;
    }

    /**
     * Get condiciones_pago.
     *
     * @return \Buseta\TallerBundle\Entity\CondicionesPago
     */
    public function getCondicionesPago()
    {
        return $this->condiciones_pago;
    }

    /**
     * Set moneda.
     *
     * @param \Buseta\NomencladorBundle\Entity\Moneda $moneda
     *
     * @return NecesidadMaterial
     */
    public function setMoneda(\Buseta\NomencladorBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda.
     *
     * @return \Buseta\NomencladorBundle\Entity\Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return NecesidadMaterial
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return NecesidadMaterial
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set deleted.
     *
     * @param \DateTime $deleted
     *
     * @return NecesidadMaterial
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted.
     *
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set createdby.
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $createdby
     *
     * @return NecesidadMaterial
     */
    public function setCreatedby(\HatueySoft\SecurityBundle\Entity\User $createdby = null)
    {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get createdby.
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * Set updatedby.
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $updatedby
     *
     * @return NecesidadMaterial
     */
    public function setUpdatedby(\HatueySoft\SecurityBundle\Entity\User $updatedby = null)
    {
        $this->updatedby = $updatedby;

        return $this;
    }

    /**
     * Get updatedby.
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getUpdatedby()
    {
        return $this->updatedby;
    }

    /**
     * Set deletedby.
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $deletedby
     *
     * @return NecesidadMaterial
     */
    public function setDeletedby(\HatueySoft\SecurityBundle\Entity\User $deletedby = null)
    {
        $this->deletedby = $deletedby;

        return $this;
    }

    /**
     * Get deletedby.
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getDeletedby()
    {
        return $this->deletedby;
    }

    /**
     * Set descuento
     *
     * @param string $descuento
     * @return NecesidadMaterial
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return float|null
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set importeDescuento
     *
     * @param string $importeDescuento
     * @return NecesidadMaterial
     */
    public function setImporteDescuento($importeDescuento)
    {
        $this->importeDescuento = $importeDescuento;

        return $this;
    }

    /**
     * Get importeDescuento
     *
     * @return float|null
     */
    public function getImporteDescuento()
    {
        return $this->importeDescuento;
    }

    /**
     * Set importeImpuesto
     *
     * @param string $importeImpuesto
     * @return NecesidadMaterial
     */
    public function setImporteImpuesto($importeImpuesto)
    {
        $this->importeImpuesto = $importeImpuesto;

        return $this;
    }

    /**
     * Get importeImpuesto
     *
     * @return float|null
     */
    public function getImporteImpuesto()
    {
        return $this->importeImpuesto;
    }

    /**
     * Set impuesto
     *
     * @param \Buseta\TallerBundle\Entity\Impuesto $impuesto
     * @return NecesidadMaterial
     */
    public function setImpuesto(\Buseta\TallerBundle\Entity\Impuesto $impuesto = null)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto
     *
     * @return \Buseta\TallerBundle\Entity\Impuesto
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->created = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreFlush
     */
    public function updateImporteTotal()
    {
        $this->importe_total_lineas = 0;
        $this->importeDescuento = 0;
        $this->importeImpuesto = 0;
        $this->importe_total = 0;

        $factorDescuento = 0;
        if ($this->descuento !== null && $this->descuento !== 0) {
            $factorDescuento = $this->descuento / 100;
        }

        $factorImpuesto = 0;
        if ($this->impuesto !== null) {
            $factorImpuesto = $this->impuesto->getTarifa() / 100;
        }

        foreach ($this->necesidad_material_lineas as $linea) {
            /** @var NecesidadMaterialLinea $linea */
            $importeLinea = $linea->getPrecioUnitario() * $linea ->getCantidadPedido();

            // descuento por linea
            $factorDescuentoLinea = 0;
            if($linea->getPorcientoDescuento() !== null && $linea->getPorcientoDescuento() !== 0) {
                $factorDescuentoLinea = $linea->getPorcientoDescuento() / 100;
            }
            $descuento = $factorDescuento + $factorDescuentoLinea;
            $importeDescuento = $importeLinea * $descuento;

            // impuesto por linea
            $factorImpuestoLinea = 0;
            $importeImpuesto = 0;
            if ($linea->getImpuesto() !== null) {
                if ($linea->getImpuesto()->getTipo() == 'porcentaje') {
                    $factorImpuestoLinea = $linea->getImpuesto()->getTarifa() / 100;
                    $impuesto = $factorImpuesto + $factorImpuestoLinea;
                    $importeImpuesto = $importeLinea * $impuesto;
                } else {
                    $importeImpuesto = $linea->getImpuesto()->getTarifa();
                }

            }

            // importe total (formula reducida)
            $importeTotal = $importeLinea + $importeImpuesto - $importeDescuento;

            $this->importeDescuento += $importeDescuento;
            $this->importeImpuesto += $importeImpuesto;
            $this->importe_total_lineas += $importeLinea;
            $this->importe_total += $importeTotal;
        }
    }
}

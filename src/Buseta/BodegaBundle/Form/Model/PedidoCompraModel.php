<?php

namespace Buseta\BodegaBundle\Form\Model;

use Buseta\BodegaBundle\Entity\PedidoCompra;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PedidoCompra Model
 *
 */
class PedidoCompraModel
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
     */
    private $observaciones;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $numeroReferencia;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     * @Assert\NotBlank()
     */
    private $tercero;

    /**
     * @var \DateTime
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
    private $pedido_compra_lineas;

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
    public function __construct(PedidoCompra $pedidocompra = null)
    {
        $this->pedido_compra_lineas = new \Doctrine\Common\Collections\ArrayCollection();

        if ($pedidocompra !== null) {
            $this->id = $pedidocompra->getId();
            $this->created = $pedidocompra->getCreated();
            $this->createdby = $pedidocompra->getCreatedby();
            $this->numeroReferencia = $pedidocompra->getNumeroReferencia();
            $this->deleted = $pedidocompra->getDeleted();
            $this->deletedby = $pedidocompra->getDeletedby();
            $this->updated = $pedidocompra->getUpdated();
            $this->updatedby = $pedidocompra->getUpdatedby();
            $this->estado_documento = $pedidocompra->getEstadoDocumento();
            $this->fecha_pedido = $pedidocompra->getFechaPedido();
            $this->importeCompra = $pedidocompra->getImporteCompra();
            $this->importe_total = $pedidocompra->getImporteTotal();
            $this->importe_total_lineas = $pedidocompra->getImporteTotalLineas();
            $this->numero_documento = $pedidocompra->getNumeroDocumento();
            $this->observaciones = $pedidocompra->getObservaciones();
            $this->descuento        = $pedidocompra->getDescuento();
            $this->impuesto         = $pedidocompra->getImpuesto();
            $this->importeDescuento = $pedidocompra->getImporteDescuento();
            $this->importeImpuesto  = $pedidocompra->getImporteImpuesto();

            if ($pedidocompra->getTercero()) {
                $this->tercero  = $pedidocompra->getTercero();
            }
            if ($pedidocompra->getAlmacen()) {
                $this->almacen  = $pedidocompra->getAlmacen();
            }
            if ($pedidocompra->getMoneda()) {
                $this->moneda  = $pedidocompra->getMoneda();
            }
            if ($pedidocompra->getFormaPago()) {
                $this->forma_pago  = $pedidocompra->getFormaPago();
            }
            if ($pedidocompra->getCondicionesPago()) {
                $this->condiciones_pago  = $pedidocompra->getCondicionesPago();
            }
            if (!$pedidocompra->getPedidoCompraLineas()->isEmpty()) {
                $this->pedido_compra_lineas = $pedidocompra->getPedidoCompraLineas();
            } else {
                $this->pedido_compra_lineas = new ArrayCollection();
            }
        }
    }

    /**
     * @return PedidoCompra
     */
    public function getEntityData()
    {
        $pedidocompra = new PedidoCompra();
        $pedidocompra->setCreated($this->getCreated());
        $pedidocompra->setCreatedby($this->getCreatedby());
        $pedidocompra->setNumeroReferencia($this->getNumeroReferencia());
        $pedidocompra->setDeleted($this->getDeleted());
        $pedidocompra->setDeletedby($this->getDeletedby());
        $pedidocompra->setEstadoDocumento($this->getEstadoDocumento());
        $pedidocompra->setFechaPedido($this->getFechaPedido());
        $pedidocompra->setImporteCompra($this->getImporteCompra());
        $pedidocompra->setImporteTotal($this->getImporteTotal());
        $pedidocompra->setImporteTotalLineas($this->getImporteTotalLineas());
        $pedidocompra->setNumeroDocumento($this->getNumeroDocumento());
        $pedidocompra->setUpdated($this->getUpdated());
        $pedidocompra->setUpdatedby($this->getUpdatedby());
        $pedidocompra->setObservaciones($this->getObservaciones());
        $pedidocompra->setImpuesto($this->getImpuesto());
        $pedidocompra->setDescuento($this->getDescuento());
        $pedidocompra->setImporteImpuesto($this->getImporteImpuesto());
        $pedidocompra->setImporteDescuento($this->getImporteDescuento());

        if ($this->getTercero() !== null) {
            $pedidocompra->setTercero($this->getTercero());
        }

        if ($this->getAlmacen() !== null) {
            $pedidocompra->setAlmacen($this->getAlmacen());
        }

        if ($this->getMoneda() !== null) {
            $pedidocompra->setMoneda($this->getMoneda());
        }

        if ($this->getFormaPago() !== null) {
            $pedidocompra->setFormaPago($this->getFormaPago());
        }

        if ($this->getCondicionesPago() !== null) {
            $pedidocompra->setCondicionesPago($this->getCondicionesPago());
        }

        if (!$this->getPedidoCompraLineas()->isEmpty()) {
            foreach ($this->getPedidoCompraLineas() as $lineas) {
                $pedidocompra->addPedidoCompraLinea($lineas);
            }
        }

        return $pedidocompra;
    }

    /**
     * @return ArrayCollection
     */
    public function getPedidoCompraLineas()
    {
        return $this->pedido_compra_lineas;
    }

    /**
     * @param ArrayCollection $pedido_compra_lineas
     */
    public function setPedidoCompraLineas($pedido_compra_lineas)
    {
        $this->pedido_compra_lineas = $pedido_compra_lineas;
    }

    /**
     * @return mixed
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param mixed $observaciones
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
     * @return string
     */
    public function getNumeroReferencia()
    {
        return $this->numeroReferencia;
    }

    /**
     * @param string $numeroReferencia
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
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

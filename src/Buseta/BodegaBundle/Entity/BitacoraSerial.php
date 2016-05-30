<?php

namespace Buseta\BodegaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\BodegaBundle\Interfaces\DateTimeAwareInterface;

/**
 * BitacoraSeriales.
 *
 * @ORM\Table(name="d_bitacora_serial", indexes={@ORM\Index(name="serial_idx", columns={"serial"})})
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\BitacoraSerialRepository")
 */
class BitacoraSerial implements DateTimeAwareInterface
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
     * The allowed values for this list are:
     * C+ (Customer Returns)
     * C- (Customer Shipment)
     * D+ (Internal Consumption +)
     * D- (Internal Consumption -)
     * I+ (Inventory In)
     * I- (Inventory Out)
     * M+ (Movement To)
     * M- (Movement From)
     * P+ (Production +)
     * P- (Production -)
     * V+ (Vendor Receipts)
     * V- (Vendor Returns)
     * W+ (Work Order +)
     * W- (Work Order -)
     *
     * @var string
     *
     * @ORM\Column(name="movement_type", type="string", length=2)
     *
     * @Assert\NotNull()
     * @Assert\Choice(choices={"C+","C-","D+","D-","I+","I-","M+","M-","P+","P-","V+","V-","W+","W-"})
     */
    private $tipoMovimiento;

    /**
     * @var \Buseta\BodegaBundle\Entity\Bodega
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Bodega")
     * @ORM\JoinColumn(name="warehouse_id")
     */
    private $almacen;

    /**
     * @var \Buseta\BodegaBundle\Entity\Producto
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Producto")
     * @ORM\JoinColumn(name="product_id")
     *
     * @Assert\NotNull()
     */
    private $producto;

    /**
     * @var \Buseta\BodegaBundle\Entity\ProductoSeriado
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\ProductoSeriado")
     * @ORM\JoinColumn(name="productoseriado_id")
     */
    private $productoSeriado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="movement_date", type="datetime")
     * @Assert\Date()
     */
    private $fechaMovimiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="movement_qty", type="integer", nullable=true)
     */
    private $cantidadMovida;

    /**
     * @var integer
     *
     * @ORM\Column(name="qty", type="smallint", nullable=true)
     */
     private $cantidad;

    /**
     * @var \Buseta\BodegaBundle\Entity\InventarioFisicoLinea
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\InventarioFisicoLinea")
     * @ORM\JoinColumn(name="inventoryline_id")
     */
    private $inventarioLinea;

    /**
     * @var \Buseta\BodegaBundle\Entity\MovimientosProductos
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\MovimientosProductos")
     * @ORM\JoinColumn(name="movementline_id")
     */
    private $movimientoLinea;

    /**
     * @var \Buseta\BodegaBundle\Entity\AlbaranLinea
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\AlbaranLinea")
     * @ORM\JoinColumn(name="inoutline_id")
     */
    private $entradaSalidaLinea;

    /**
     * @var string
     *
     * @ORM\Column(name="production_line", type="string", nullable=true)
     */
    private $produccionLinea;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_consumption_line", type="string", nullable=true)
     */
    private $consumoInterno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;

    /**
     * @var string
     *
     * @ORM\Column(name="serial", type="string", length=32, nullable=true)
     */
    private $serial;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipoMovimiento
     *
     * @param string $tipoMovimiento
     * @return BitacoraSerial
     */
    public function setTipoMovimiento($tipoMovimiento)
    {
        $this->tipoMovimiento = $tipoMovimiento;

        return $this;
    }

    /**
     * Get tipoMovimiento
     *
     * @return string
     */
    public function getTipoMovimiento()
    {
        return $this->tipoMovimiento;
    }

    /**
     * Set fechaMovimiento
     *
     * @param \DateTime $fechaMovimiento
     * @return BitacoraSerial
     */
    public function setFechaMovimiento($fechaMovimiento)
    {
        $this->fechaMovimiento = $fechaMovimiento;

        return $this;
    }

    /**
     * Get fechaMovimiento
     *
     * @return \DateTime
     */
    public function getFechaMovimiento()
    {
        return $this->fechaMovimiento;
    }

    /**
     * Set cantidadMovida
     *
     * @param integer $cantidadMovida
     * @return BitacoraSerial
     */
    public function setCantidadMovida($cantidadMovida)
    {
        $this->cantidadMovida = $cantidadMovida;

        return $this;
    }

    /**
     * Get cantidadMovida
     *
     * @return integer
     */
    public function getCantidadMovida()
    {
        return $this->cantidadMovida;
    }


    /**
     * Set consumoInterno
     *
     * @param integer $consumoInterno
     * @return BitacoraSerial
     */
    public function setConsumoInterno($consumoInterno)
    {
        $this->consumoInterno = $consumoInterno;

        return $this;
    }

    /**
     * Get consumoInterno
     *
     * @return integer
     */
    public function getConsumoInterno()
    {
        return $this->consumoInterno;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BitacoraSerial
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return BitacoraSerial
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return BitacoraSerial
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set almacen
     *
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacen
     * @return BitacoraSerial
     */
    public function setAlmacen(\Buseta\BodegaBundle\Entity\Bodega $almacen = null)
    {
        $this->almacen = $almacen;

        return $this;
    }

    /**
     * Get almacen
     *
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set producto
     *
     * @param \Buseta\BodegaBundle\Entity\Producto $producto
     * @return BitacoraSerial
     */
    public function setProducto(\Buseta\BodegaBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \Buseta\BodegaBundle\Entity\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set productoSeriado
     *
     * @param \Buseta\BodegaBundle\Entity\ProductoSeriado $productoSeriado
     * @return BitacoraSerial
     */
    public function setProductoSeriado(\Buseta\BodegaBundle\Entity\ProductoSeriado $productoSeriado = null)
    {
        $this->productoSeriado = $productoSeriado;

        return $this;
    }

    /**
     * Get productoSeriado
     *
     * @return \Buseta\BodegaBundle\Entity\ProductoSeriado
     */
    public function getProductoSeriado()
    {
        return $this->productoSeriado;
    }

    /**
     * Set inventarioLinea
     *
     * @param \Buseta\BodegaBundle\Entity\InventarioFisicoLinea $inventarioLinea
     * @return BitacoraSerial
     */
    public function setInventarioLinea(\Buseta\BodegaBundle\Entity\InventarioFisicoLinea $inventarioLinea = null)
    {
        $this->inventarioLinea = $inventarioLinea;

        return $this;
    }

    /**
     * Get inventarioLinea
     *
     * @return \Buseta\BodegaBundle\Entity\InventarioFisicoLinea
     */
    public function getInventarioLinea()
    {
        return $this->inventarioLinea;
    }

    /**
     * Set movimientoLinea
     *
     * @param \Buseta\BodegaBundle\Entity\MovimientosProductos $movimientoLinea
     * @return BitacoraSerial
     */
    public function setMovimientoLinea(\Buseta\BodegaBundle\Entity\MovimientosProductos $movimientoLinea = null)
    {
        $this->movimientoLinea = $movimientoLinea;

        return $this;
    }

    /**
     * Get movimientoLinea
     *
     * @return \Buseta\BodegaBundle\Entity\MovimientosProductos
     */
    public function getMovimientoLinea()
    {
        return $this->movimientoLinea;
    }

    /**
     * Set entradaSalidaLinea
     *
     * @param \Buseta\BodegaBundle\Entity\AlbaranLinea $entradaSalidaLinea
     * @return BitacoraSerial
     */
    public function setEntradaSalidaLinea(\Buseta\BodegaBundle\Entity\AlbaranLinea $entradaSalidaLinea = null)
    {
        $this->entradaSalidaLinea = $entradaSalidaLinea;

        return $this;
    }

    /**
     * Get entradaSalidaLinea
     *
     * @return \Buseta\BodegaBundle\Entity\AlbaranLinea
     */
    public function getEntradaSalidaLinea()
    {
        return $this->entradaSalidaLinea;
    }

    /**
     * Set produccionLinea
     *
     * @param string $produccionLinea
     * @return BitacoraSerial
     */
    public function setProduccionLinea($produccionLinea)
    {
        $this->produccionLinea = $produccionLinea;

        return $this;
    }

    /**
     * Get produccionLinea
     *
     * @return string
     */
    public function getProduccionLinea()
    {
        return $this->produccionLinea;
    }

    /**
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * @param $serial
     * @return $this
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return BitacoraSerial
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
}

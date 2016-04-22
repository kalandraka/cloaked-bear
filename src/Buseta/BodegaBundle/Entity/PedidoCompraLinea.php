<?php

namespace Buseta\BodegaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PedidoCompraLinea.
 *
 * @ORM\Table(name="d_pedido_compra_linea")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\PedidoCompraLineaRepository")
 */
class PedidoCompraLinea
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
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\PedidoCompra", inversedBy="pedido_compra_lineas")
     */
    private $pedidoCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="linea", type="string", nullable=true)
     */
    private $linea;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Producto", inversedBy="pedido_compra_lineas")
     * @Assert\NotBlank()
     */
    private $producto;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_pedido", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $cantidad_pedido;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\UOM")
     * @Assert\NotBlank()
     */
    private $uom;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_unitario", type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    private $precio_unitario;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\TallerBundle\Entity\Impuesto")
     */
    private $impuesto;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Moneda")
     * @Assert\NotBlank()
     */
    private $moneda;

    /**
     * @var float
     *
     * @ORM\Column(name="porciento_descuento", type="decimal", scale=2, nullable=true)
     */
    private $porciento_descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_linea", type="decimal", scale=2)
     */
    private $importe_linea;

    public function __construct()
    {
        $this->importe_linea        = 0;
        $this->porciento_descuento  = 0;
        $this->precio_unitario      = 0;
        $this->cantidad_pedido      = 0;
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
     * Set linea.
     *
     * @param string $linea
     *
     * @return PedidoCompraLinea
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;

        return $this;
    }

    /**
     * Get linea.
     *
     * @return string
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set cantidad_pedido.
     *
     * @param integer $cantidadPedido
     *
     * @return PedidoCompraLinea
     */
    public function setCantidadPedido($cantidadPedido)
    {
        $this->cantidad_pedido = $cantidadPedido;

        return $this;
    }

    /**
     * Get cantidad_pedido.
     *
     * @return integer
     */
    public function getCantidadPedido()
    {
        return $this->cantidad_pedido;
    }

    /**
     * Set precio_unitario.
     *
     * @param float $precioUnitario
     *
     * @return PedidoCompraLinea
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precio_unitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precio_unitario.
     *
     * @return float
     */
    public function getPrecioUnitario()
    {
        return $this->precio_unitario;
    }

    /**
     * Set porciento_descuento.
     *
     * @param string $porcientoDescuento
     *
     * @return PedidoCompraLinea
     */
    public function setPorcientoDescuento($porcientoDescuento)
    {
        $this->porciento_descuento = $porcientoDescuento;

        return $this;
    }

    /**
     * Get porciento_descuento.
     *
     * @return string
     */
    public function getPorcientoDescuento()
    {
        return $this->porciento_descuento;
    }

    /**
     * Set importe_linea.
     *
     * @param float $importeLinea
     *
     * @return PedidoCompraLinea
     */
    public function setImporteLinea($importeLinea)
    {
        $this->importe_linea = $importeLinea;

        return $this;
    }

    /**
     * Get importe_linea.
     *
     * @return float
     */
    public function getImporteLinea()
    {
        return $this->importe_linea;
    }

    /**
     * Set pedidoCompra.
     *
     * @param \Buseta\BodegaBundle\Entity\PedidoCompra $pedidoCompra
     *
     * @return PedidoCompraLinea
     */
    public function setPedidoCompra(\Buseta\BodegaBundle\Entity\PedidoCompra $pedidoCompra = null)
    {
        $this->pedidoCompra = $pedidoCompra;

        return $this;
    }

    /**
     * Get pedidoCompra.
     *
     * @return \Buseta\BodegaBundle\Entity\PedidoCompra
     */
    public function getPedidoCompra()
    {
        return $this->pedidoCompra;
    }

    /**
     * Set producto.
     *
     * @param \Buseta\BodegaBundle\Entity\Producto $producto
     *
     * @return PedidoCompraLinea
     */
    public function setProducto(\Buseta\BodegaBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto.
     *
     * @return \Buseta\BodegaBundle\Entity\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set uom.
     *
     * @param \Buseta\NomencladorBundle\Entity\UOM $uom
     *
     * @return PedidoCompraLinea
     */
    public function setUom(\Buseta\NomencladorBundle\Entity\UOM $uom = null)
    {
        $this->uom = $uom;

        return $this;
    }

    /**
     * Get uom.
     *
     * @return \Buseta\NomencladorBundle\Entity\UOM
     */
    public function getUom()
    {
        return $this->uom;
    }

    /**
     * Set impuesto.
     *
     * @param \Buseta\TallerBundle\Entity\Impuesto $impuesto
     *
     * @return PedidoCompraLinea
     */
    public function setImpuesto(\Buseta\TallerBundle\Entity\Impuesto $impuesto = null)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto.
     *
     * @return \Buseta\TallerBundle\Entity\Impuesto
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set moneda.
     *
     * @param \Buseta\NomencladorBundle\Entity\Moneda $moneda
     *
     * @return PedidoCompraLinea
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
}

<?php

namespace Buseta\BodegaBundle\Form\Model;

use Buseta\BodegaBundle\Entity\Interfaces\PedidoCompraInterface;
use Buseta\BodegaBundle\Entity\PedidoCompra;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Form\Model\Converters\PedidoCompraConverter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PedidoCompra Model
 *
 */
class PedidoCompraModel implements PedidoCompraInterface
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
     */
    private $pedidoCompraLineas;


    /**
     * Constructor
     *
     * @param PedidoCompra $pedidocompra
     */
    public function __construct(PedidoCompra $pedidocompra = null)
    {
        $this->pedidoCompraLineas = new \Doctrine\Common\Collections\ArrayCollection();

        if ($pedidocompra !== null) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            $pedidoCompraModel = PedidoCompraConverter::getModel($pedidocompra);
            $properties = get_class_vars(__CLASS__);
            foreach ($properties as $property => $value) {
                $propertyAccessor->setValue(
                    $this,
                    $property,
                    $propertyAccessor->getValue($pedidoCompraModel, $property)
                );
            }
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getPedidoCompraLineas()
    {
        return $this->pedidoCompraLineas;
    }

    /**
     * @param ArrayCollection $pedidoCompraLineas
     *
     * @return $this
     */
    public function setPedidoCompraLineas(\Doctrine\Common\Collections\ArrayCollection $pedidoCompraLineas)
    {
        $this->pedidoCompraLineas = $pedidoCompraLineas;

        return $this;
    }

    /**
     * @param $pedidoCompraLinea
     *
     * @return $this
     */
    public function addPedidoCompraLinea(PedidoCompraLinea $pedidoCompraLinea)
    {
        $this->pedidoCompraLineas->add($pedidoCompraLinea);

        return $this;
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
     *
     * @deprecated Use getBodega() instead
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * @param mixed $almacen
     *
     * @deprecated Use setBodega(\Buseta\BodegaBundle\Entity\Bodega $bodega) instead
     */
    public function setAlmacen($almacen)
    {
        $this->almacen = $almacen;
    }

    /**
     * @param \Buseta\BodegaBundle\Entity\Bodega $bodega
     *
     * @return PedidoCompraInterface
     */
    public function setBodega(\Buseta\BodegaBundle\Entity\Bodega $bodega)
    {
        $this->almacen = $bodega;

        return $this;
    }

    /**
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getBodega()
    {
        return $this->almacen;
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

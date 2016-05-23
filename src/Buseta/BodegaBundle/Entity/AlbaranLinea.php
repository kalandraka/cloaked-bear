<?php

namespace Buseta\BodegaBundle\Entity;

use Buseta\BodegaBundle\Interfaces\GeneradorBitacoraInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\BodegaBundle\Validator\Constraints\ValidarSerial;

//!TODO: Eliminar campo almacen y utilizar únicamente la bodega registrada en Albaran.
/**
 * AlbaranLinea.
 * @ValidarSerial()
 * @ORM\Table(name="d_albaran_linea")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\AlbaranLineaRepository")
 */
class AlbaranLinea implements GeneradorBitacoraInterface
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
     * @ORM\Column(name="valorAtributos", type="string", nullable=true)
     */
    private $valorAtributos;

    /**
     * @var \Buseta\BodegaBundle\Entity\Producto
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Producto", inversedBy="albaranLinea")
     * @Assert\NotBlank()
     */
    private $producto;

    /**
     * @var \Buseta\NomencladorBundle\Entity\UOM
     *
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\UOM")
     * @Assert\NotBlank()
     */
    private $uom;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidadMovida", type="integer")
     * @Assert\NotBlank()
     */
    private $cantidadMovida;

    /**
     * @var \Buseta\BodegaBundle\Entity\Albaran
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Albaran", inversedBy="albaranLineas")
     */
    private $albaran;

    /**
     * @var string
     *
     * @ORM\Column(name="seriales", type="string", nullable=true)
     */
    private $seriales;

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
     * Set valorAtributos.
     *
     * @param string $valorAtributos
     *
     * @return AlbaranLinea
     */
    public function setValorAtributos($valorAtributos)
    {
        $this->valorAtributos = $valorAtributos;

        return $this;
    }

    /**
     * Get valorAtributos.
     *
     * @return string
     */
    public function getValorAtributos()
    {
        return $this->valorAtributos;
    }

    /**
     * Set cantidadMovida.
     *
     * @param integer $cantidadMovida
     *
     * @return AlbaranLinea
     */
    public function setCantidadMovida($cantidadMovida)
    {
        $this->cantidadMovida = $cantidadMovida;

        return $this;
    }

    /**
     * Get cantidadMovida.
     *
     * @return integer
     */
    public function getCantidadMovida()
    {
        return $this->cantidadMovida;
    }

    /**
     * Set producto.
     *
     * @param \Buseta\BodegaBundle\Entity\Producto $producto
     *
     * @return AlbaranLinea
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
     * @return AlbaranLinea
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
     * Set albaran.
     *
     * @param \Buseta\BodegaBundle\Entity\Albaran $albaran
     *
     * @return AlbaranLinea
     */
    public function setAlbaran(\Buseta\BodegaBundle\Entity\Albaran $albaran = null)
    {
        $this->albaran = $albaran;

        return $this;
    }

    /**
     * Get albaran.
     *
     * @return \Buseta\BodegaBundle\Entity\Albaran
     */
    public function getAlbaran()
    {
        return $this->albaran;
    }

    /**
     * @return string
     */
    public function getSeriales()
    {
        return $this->seriales;
    }

    /**
     * @param string $seriales
     *
     * @return AlbaranLinea
     */
    public function setSeriales($seriales)
    {
        $this->seriales = $seriales;
        return $this;
    }


}

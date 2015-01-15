<?php

namespace Buseta\BodegaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Albaran
 *
 * @ORM\Table(name="d_albaran")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\AlbaranRepository")
 */
class Albaran
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
     * @ORM\Column(name="numeroReferencia", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $numeroReferencia;

    /**
     * @var string
     *
     * @ORM\Column(name="consecutivoCompra", type="string", nullable=true)
     */
    private $consecutivoCompra;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Tercero", inversedBy="albaran")
     */
    private $tercero;

    /**
     * @var date
     *
     * @ORM\Column(name="fechaMovimiento", type="date", nullable=true)
     * @Assert\Date()
     */
    private $fechaMovimiento;

    /**
     * @var date
     *
     * @ORM\Column(name="fechaContable", type="date", nullable=true)
     * @Assert\Date()
     */
    private $fechaContable;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Bodega", inversedBy="albaran")
     */
    private $almacen;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoDocumento", type="string", nullable=false)
     */
    private $estadoDocumento;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\PedidoCompra")
     */
    private $pedidoCompra;


    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Buseta\BodegaBundle\Entity\AlbaranLinea", mappedBy="albaran", cascade={"all"})
     */
    private $albaranLinea;

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
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return Albaran
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
    
        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia()
    {
        return $this->numeroReferencia;
    }

    /**
     * Set consecutivoCompra
     *
     * @param string $consecutivoCompra
     * @return Albaran
     */
    public function setConsecutivoCompra($consecutivoCompra)
    {
        $this->consecutivoCompra = $consecutivoCompra;
    
        return $this;
    }

    /**
     * Get consecutivoCompra
     *
     * @return string 
     */
    public function getConsecutivoCompra()
    {
        return $this->consecutivoCompra;
    }

    /**
     * Set fechaMovimiento
     *
     * @param \DateTime $fechaMovimiento
     * @return Albaran
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
     * Set fechaContable
     *
     * @param \DateTime $fechaContable
     * @return Albaran
     */
    public function setFechaContable($fechaContable)
    {
        $this->fechaContable = $fechaContable;
    
        return $this;
    }

    /**
     * Get fechaContable
     *
     * @return \DateTime 
     */
    public function getFechaContable()
    {
        return $this->fechaContable;
    }

    /**
     * Set estadoDocumento
     *
     * @param string $estadoDocumento
     * @return Albaran
     */
    public function setEstadoDocumento($estadoDocumento)
    {
        $this->estadoDocumento = $estadoDocumento;
    
        return $this;
    }

    /**
     * Get estadoDocumento
     *
     * @return string 
     */
    public function getEstadoDocumento()
    {
        return $this->estadoDocumento;
    }

    /**
     * Set tercero
     *
     * @param \Buseta\BodegaBundle\Entity\Tercero $tercero
     * @return Albaran
     */
    public function setTercero(\Buseta\BodegaBundle\Entity\Tercero $tercero = null)
    {
        $this->tercero = $tercero;
    
        return $this;
    }

    /**
     * Get tercero
     *
     * @return \Buseta\BodegaBundle\Entity\Tercero 
     */
    public function getTercero()
    {
        return $this->tercero;
    }

    /**
     * Set almacen
     *
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacen
     * @return Albaran
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
     * Constructor
     */
    public function __construct()
    {
        $this->albaranLinea = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add albaranLinea
     *
     * @param \Buseta\BodegaBundle\Entity\AlbaranLinea $albaranLinea
     * @return Albaran
     */
    public function addAlbaranLinea(\Buseta\BodegaBundle\Entity\AlbaranLinea $albaranLinea)
    {
        $albaranLinea->setAlbaran($this);

        $this->albaranLinea[] = $albaranLinea;
    
        return $this;
    }

    /**
     * Remove albaranLinea
     *
     * @param \Buseta\BodegaBundle\Entity\AlbaranLinea $albaranLinea
     */
    public function removeAlbaranLinea(\Buseta\BodegaBundle\Entity\AlbaranLinea $albaranLinea)
    {
        $this->albaranLinea->removeElement($albaranLinea);
    }

    /**
     * Get albaranLinea
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbaranLinea()
    {
        return $this->albaranLinea;
    }

    /**
     * Set pedidoCompra
     *
     * @param \Buseta\BodegaBundle\Entity\PedidoCompra $pedidoCompra
     * @return Albaran
     */
    public function setPedidoCompra(\Buseta\BodegaBundle\Entity\PedidoCompra $pedidoCompra = null)
    {
        $this->pedidoCompra = $pedidoCompra;
    
        return $this;
    }

    /**
     * Get pedidoCompra
     *
     * @return \Buseta\BodegaBundle\Entity\PedidoCompra 
     */
    public function getPedidoCompra()
    {
        return $this->pedidoCompra;
    }
}
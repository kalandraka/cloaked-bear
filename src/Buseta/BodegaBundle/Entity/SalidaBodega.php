<?php

namespace Buseta\BodegaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SalidaBodega.
 *
 * @ORM\Table(name="d_salida_bodega")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\SalidaBodegaRepository")
 */
class SalidaBodega
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
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Bodega")
     */
    private $almacenOrigen;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Bodega")
     */
    private $almacenDestino;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BusesBundle\Entity\Autobus")
     */
    private $centroCosto;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Tercero")
     */
    private $responsable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     * @Assert\Date()
     * @Assert\NotNull()
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoOT", type="string", nullable=true)
     * @Assert\Choice(choices={"rapida", "normal"})
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    private $tipoOT;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\TallerBundle\Entity\OrdenTrabajo")
     */
    private $ordenTrabajo;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Buseta\BodegaBundle\Entity\SalidaBodegaProducto", mappedBy="salida", cascade={"all"})
     * @Assert\Valid()
     */
    private $salidas_productos;

    /**
     * @var string
     *
     * @ORM\Column(name="controlEntregaMaterial", type="string")
     * @Assert\NotBlank
     */
    private $controlEntregaMaterial;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="estado_documento", type="string")
     * @Assert\NotBlank()
     */
    private $estado_documento = 'BO';

    /**
     * @var string
     *
     * @ORM\Column(name="movidoBy", type="string", nullable=true)
     */
    private $movidoBy;

    /**
     * @var date
     *
     * @ORM\Column(name="created", type="date")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="createdBy", type="string", nullable=true)
     */
    private $createdBy;

    /**
     * @var date
     *
     * @ORM\Column(name="updated", type="date")
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="updatedBy", type="string", nullable=true)
     */
    private $updatedBy;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return SalidaBodega
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return SalidaBodega
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set tipoOT
     *
     * @param string $tipoOT
     * @return SalidaBodega
     */
    public function setTipoOT($tipoOT)
    {
        $this->tipoOT = $tipoOT;

        return $this;
    }

    /**
     * Get tipoOT
     *
     * @return string
     */
    public function getTipoOT()
    {
        return $this->tipoOT;
    }

    /**
     * Set controlEntregaMaterial
     *
     * @param string $controlEntregaMaterial
     * @return SalidaBodega
     */
    public function setControlEntregaMaterial($controlEntregaMaterial)
    {
        $this->controlEntregaMaterial = $controlEntregaMaterial;

        return $this;
    }

    /**
     * Get controlEntregaMaterial
     *
     * @return string
     */
    public function getControlEntregaMaterial()
    {
        return $this->controlEntregaMaterial;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return SalidaBodega
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set movidoBy
     *
     * @param string $movidoBy
     * @return SalidaBodega
     */
    public function setMovidoBy($movidoBy)
    {
        $this->movidoBy = $movidoBy;

        return $this;
    }

    /**
     * Get movidoBy
     *
     * @return string
     */
    public function getMovidoBy()
    {
        return $this->movidoBy;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return SalidaBodega
     */
    public function setCreated($created)
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
     * Set createdBy
     *
     * @param string $createdBy
     * @return SalidaBodega
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return SalidaBodega
     */
    public function setUpdated($updated)
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
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return SalidaBodega
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set almacenOrigen
     *
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacenOrigen
     * @return SalidaBodega
     */
    public function setAlmacenOrigen(\Buseta\BodegaBundle\Entity\Bodega $almacenOrigen = null)
    {
        $this->almacenOrigen = $almacenOrigen;

        return $this;
    }

    /**
     * Get almacenOrigen
     *
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getAlmacenOrigen()
    {
        return $this->almacenOrigen;
    }

    /**
     * Set almacenDestino
     *
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacenDestino
     * @return SalidaBodega
     */
    public function setAlmacenDestino(\Buseta\BodegaBundle\Entity\Bodega $almacenDestino = null)
    {
        $this->almacenDestino = $almacenDestino;

        return $this;
    }

    /**
     * Get almacenDestino
     *
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getAlmacenDestino()
    {
        return $this->almacenDestino;
    }

    /**
     * Set centroCosto
     *
     * @param \Buseta\BusesBundle\Entity\Autobus $centroCosto
     * @return SalidaBodega
     */
    public function setCentroCosto(\Buseta\BusesBundle\Entity\Autobus $centroCosto = null)
    {
        $this->centroCosto = $centroCosto;

        return $this;
    }

    /**
     * Get centroCosto
     *
     * @return \Buseta\BusesBundle\Entity\Autobus
     */
    public function getCentroCosto()
    {
        return $this->centroCosto;
    }

    /**
     * Set responsable
     *
     * @param \Buseta\BodegaBundle\Entity\Tercero $responsable
     * @return SalidaBodega
     */
    public function setResponsable(\Buseta\BodegaBundle\Entity\Tercero $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return \Buseta\BodegaBundle\Entity\Tercero
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set ordenTrabajo
     *
     * @param \Buseta\TallerBundle\Entity\OrdenTrabajo $ordenTrabajo
     * @return SalidaBodega
     */
    public function setOrdenTrabajo(\Buseta\TallerBundle\Entity\OrdenTrabajo $ordenTrabajo = null)
    {
        $this->ordenTrabajo = $ordenTrabajo;

        return $this;
    }

    /**
     * Get ordenTrabajo
     *
     * @return \Buseta\TallerBundle\Entity\OrdenTrabajo
     */
    public function getOrdenTrabajo()
    {
        return $this->ordenTrabajo;
    }

    /**
     * Add salidas_productos
     *
     * @param \Buseta\BodegaBundle\Entity\SalidaBodegaProducto $salidasProductos
     * @return SalidaBodega
     */
    public function addSalidasProducto(\Buseta\BodegaBundle\Entity\SalidaBodegaProducto $salidasProductos)
    {
        $salidasProductos->setSalida($this);

        $this->salidas_productos[] = $salidasProductos;

        return $this;
    }

    /**
     * Remove salidas_productos
     *
     * @param \Buseta\BodegaBundle\Entity\SalidaBodegaProducto $salidasProductos
     */
    public function removeSalidasProducto(\Buseta\BodegaBundle\Entity\SalidaBodegaProducto $salidasProductos)
    {
        $this->salidas_productos->removeElement($salidasProductos);
    }

    /**
     * Get salidas_productos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSalidasProductos()
    {
        return $this->salidas_productos;
    }

    /**
     * Set estado_documento
     *
     * @param string $estadoDocumento
     * @return SalidaBodega
     */
    public function setEstadoDocumento($estadoDocumento)
    {
        $this->estado_documento = $estadoDocumento;

        return $this;
    }

    /**
     * Get estado_documento
     *
     * @return string
     */
    public function getEstadoDocumento()
    {
        return $this->estado_documento;
    }
}

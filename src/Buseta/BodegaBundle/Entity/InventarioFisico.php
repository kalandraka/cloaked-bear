<?php

namespace Buseta\BodegaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\BodegaBundle\Form\Model\InventarioFisicoModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InventarioFisico.
 *
 * @ORM\Table(name="d_inventario_fisico")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\InventarioFisicoRepository")
 */
class InventarioFisico
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
    private $almacen;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string")
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     *
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string")
     * @Assert\NotBlank()
     */
    private $estado = 'BO';

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Buseta\BodegaBundle\Entity\InventarioFisicoLinea", mappedBy="inventarioFisico", cascade={"all"})
     */
    private $inventarioFisicoLineas;

    /**
     * @param InventarioFisicoModel $model
     * @return InventarioFisico
     */
    public function setModelData(InventarioFisicoModel $model)
    {
        $this->id = $model->getId();
        $this->nombre = $model->getNombre();
        $this->descripcion = $model->getDescripcion();
        $this->fecha = $model->getFecha();
        $this->estado = $model->getEstado();

        if ($model->getAlmacen()) {
            $this->almacen  = $model->getAlmacen();
        }
        if (!$model->getInventarioFisicoLinea()->isEmpty()) {
            $this->inventarioFisicoLineas = $model->getInventarioFisicoLinea();
        } else {
            $this->inventarioFisicoLineas = new ArrayCollection();
        }

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
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return InventarioFisico
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return InventarioFisico
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return InventarioFisico
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
     * Set almacen
     *
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacen
     * @return InventarioFisico
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
        $this->inventarioFisicoLineas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add inventarioFisicoLineas
     *
     * @param \Buseta\BodegaBundle\Entity\InventarioFisicoLinea $inventarioFisicoLineas
     * @return InventarioFisico
     */
    public function addInventarioFisicoLinea(\Buseta\BodegaBundle\Entity\InventarioFisicoLinea $inventarioFisicoLineas)
    {
        $inventarioFisicoLineas->setInventarioFisico($this);
        $this->inventarioFisicoLineas[] = $inventarioFisicoLineas;

        return $this;
    }

    /**
     * Remove inventarioFisicoLineas
     *
     * @param \Buseta\BodegaBundle\Entity\InventarioFisicoLinea $inventarioFisicoLineas
     */
    public function removeInventarioFisicoLinea(\Buseta\BodegaBundle\Entity\InventarioFisicoLinea $inventarioFisicoLineas)
    {
        $this->inventarioFisicoLineas->removeElement($inventarioFisicoLineas);
    }

    /**
     * Get inventarioFisicoLineas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInventarioFisicoLineas()
    {
        return $this->inventarioFisicoLineas;
    }
}

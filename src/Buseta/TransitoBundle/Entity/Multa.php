<?php

namespace Buseta\TransitoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Multa
 *
 * @ORM\Table(name="t_multa")
 * @ORM\Entity(repositoryClass="Buseta\TransitoBundle\Entity\Repository\MultaRepository")
 */
class Multa
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
     * @ORM\Column(name="descripcion", type="string", length=128)
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="num_boleta", type="string", length=32)
     */
    protected $numBoleta;

    /**
     * @var string
     *
     * @ORM\Column(name="num_articulo", type="string", length=32)
     */
    protected $numArticulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     * @Assert\Date()
     * @Assert\NotNull()
     */
    private $fecha;

    /**
     * @var \Buseta\BusesBundle\Entity\Chofer
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BusesBundle\Entity\Chofer")
     */
    private $chofer;

    /**
     * @var \Buseta\BusesBundle\Entity\Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BusesBundle\Entity\Vehiculo")
     */
    private $vehiculo;

    /**
     * @var boolean
     * @ORM\Column(name="apelada", type="boolean")
     */
    private $apelada = false;

    /**
     * @var boolean
     * @ORM\Column(name="ganada", type="boolean")
     */
    private $ganada = false;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    private $importe = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_abogado", type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    private $importeAbogado = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string")
     * @Assert\NotBlank()
     */
    private $estado = 'BO';

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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Multa
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
     * Set numBoleta
     *
     * @param string $numBoleta
     *
     * @return Multa
     */
    public function setNumBoleta($numBoleta)
    {
        $this->numBoleta = $numBoleta;

        return $this;
    }

    /**
     * Get numBoleta
     *
     * @return string
     */
    public function getNumBoleta()
    {
        return $this->numBoleta;
    }

    /**
     * Set numArticulo
     *
     * @param string $numArticulo
     *
     * @return Multa
     */
    public function setNumArticulo($numArticulo)
    {
        $this->numArticulo = $numArticulo;

        return $this;
    }

    /**
     * Get numArticulo
     *
     * @return string
     */
    public function getNumArticulo()
    {
        return $this->numArticulo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Multa
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
     * Set apelada
     *
     * @param boolean $apelada
     *
     * @return Multa
     */
    public function setApelada($apelada)
    {
        $this->apelada = $apelada;

        return $this;
    }

    /**
     * Get apelada
     *
     * @return boolean
     */
    public function getApelada()
    {
        return $this->apelada;
    }

    /**
     * Set ganada
     *
     * @param boolean $ganada
     *
     * @return Multa
     */
    public function setGanada($ganada)
    {
        $this->ganada = $ganada;

        return $this;
    }

    /**
     * Get ganada
     *
     * @return boolean
     */
    public function getGanada()
    {
        return $this->ganada;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return Multa
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set importeAbogado
     *
     * @param string $importeAbogado
     *
     * @return Multa
     */
    public function setImporteAbogado($importeAbogado)
    {
        $this->importeAbogado = $importeAbogado;

        return $this;
    }

    /**
     * Get importeAbogado
     *
     * @return string
     */
    public function getImporteAbogado()
    {
        return $this->importeAbogado;
    }

    /**
     * Set chofer
     *
     * @param \Buseta\BusesBundle\Entity\Chofer $chofer
     *
     * @return Multa
     */
    public function setChofer(\Buseta\BusesBundle\Entity\Chofer $chofer = null)
    {
        $this->chofer = $chofer;

        return $this;
    }

    /**
     * Get chofer
     *
     * @return \Buseta\BusesBundle\Entity\Chofer
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * Set vehiculo
     *
     * @param \Buseta\BusesBundle\Entity\Vehiculo $vehiculo
     *
     * @return Multa
     */
    public function setVehiculo(\Buseta\BusesBundle\Entity\Vehiculo $vehiculo = null)
    {
        $this->vehiculo = $vehiculo;

        return $this;
    }

    /**
     * Get vehiculo
     *
     * @return \Buseta\BusesBundle\Entity\Vehiculo
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return Multa
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }
}

<?php

namespace Buseta\TransitoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Accidente
 *
 * @ORM\Table(name="t_accidente")
 * @ORM\Entity(repositoryClass="Buseta\TransitoBundle\Entity\Repository\AccidenteRepository")
 */
class Accidente
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
     * @ORM\Column(name="descripcion", type="string")
     */
    protected $descripcion;

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
     * @var float
     *
     * @ORM\Column(name="importe", type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    private $importe = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string")
     * @Assert\NotBlank()
     */
    private $estado = 'BO';

    /**
     * @var string
     *
     * The allowed values for this are:
     *
     * NOPARTE
     * TRANSITO
     * PENAL
     *
     * @ORM\Column(name="parte", type="string")
     */
    private $parte = "";

    /**
     * @var string
     *
     * The allowed values for this are:
     *
     * NOHUBO
     * CHOFER
     * TERCERO
     * EMPRESA
     *
     * @ORM\Column(name="responsable", type="string")
     * @Assert\Choice(choices={"NOHUBO", "CHOFER", "TERCERO", "EMPRESA"})
     */
    private $responsable = "";

    /**
     * @var string
     *
     * The allowed values for this are:
     *
     * NADIE
     * CHOFER
     * TERCERO
     * EMPRESA
     * TALLER
     * SEGURO
     *
     * @ORM\Column(name="quien_paga", type="string")
     * @Assert\Choice(choices={"NADIE", "CHOFER", "TERCERO", "EMPRESA", "TALLER", "SEGURO"})
     */
    private $quienPaga = "";

    /**
     * @var boolean
     * @ORM\Column(name="conciliacion", type="boolean")
     */
    private $conciliacion = false;

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
     * @return Accidente
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
     * @return Accidente
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
     * Set importe
     *
     * @param string $importe
     *
     * @return Accidente
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
     * Set estado
     *
     * @param string $estado
     *
     * @return Accidente
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

    /**
     * Set parte
     *
     * @param string $parte
     *
     * @return Accidente
     */
    public function setParte($parte)
    {
        $this->parte = $parte;

        return $this;
    }

    /**
     * Get parte
     *
     * @return string
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set responsable
     *
     * @param string $responsable
     *
     * @return Accidente
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set quienPaga
     *
     * @param string $quienPaga
     *
     * @return Accidente
     */
    public function setQuienPaga($quienPaga)
    {
        $this->quienPaga = $quienPaga;

        return $this;
    }

    /**
     * Get quienPaga
     *
     * @return string
     */
    public function getQuienPaga()
    {
        return $this->quienPaga;
    }

    /**
     * Set conciliacion
     *
     * @param boolean $conciliacion
     *
     * @return Accidente
     */
    public function setConciliacion($conciliacion)
    {
        $this->conciliacion = $conciliacion;

        return $this;
    }

    /**
     * Get conciliacion
     *
     * @return boolean
     */
    public function getConciliacion()
    {
        return $this->conciliacion;
    }

    /**
     * Set chofer
     *
     * @param \Buseta\BusesBundle\Entity\Chofer $chofer
     *
     * @return Accidente
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
     * @return Accidente
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
}

<?php

namespace Buseta\TransitoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Juicio
 *
 * @ORM\Table(name="t_juicio")
 * @ORM\Entity(repositoryClass="Buseta\TransitoBundle\Entity\Repository\JuicioRepository")
 */
class Juicio
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
     * @var string
     *
     * The allowed values for this are:
     *
     * NORESPONSABLE
     * RESPONSABLE
     * EMPATE
     *
     * @ORM\Column(name="resultado", type="string")
     * @Assert\Choice(choices={"NORESPONSABLE", "RESPONSABLE", "EMPATE"})
     */
    protected $resultado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date")
     * @Assert\Date()
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date")
     * @Assert\Date()
     */
    private $fechaFin;

    /**
     * @var \Buseta\TransitoBundle\Entity\Accidente
     *
     * @ORM\ManyToOne(targetEntity="Buseta\TransitoBundle\Entity\Accidente")
     */
    private $accidente;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_abogado", type="decimal", scale=2)
     */
    private $importeAbogado = 0;

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
    private $responsable = "NOHUBO";

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
    private $quienPaga = "NADIE";

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
     * Set resultado
     *
     * @param string $resultado
     *
     * @return Juicio
     */
    public function setResultado($resultado)
    {
        $this->resultado = $resultado;

        return $this;
    }

    /**
     * Get resultado
     *
     * @return string
     */
    public function getResultado()
    {
        return $this->resultado;
    }

    /**
     * Set importeAbogado
     *
     * @param string $importeAbogado
     *
     * @return Juicio
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
     * Set accidente
     *
     * @param \Buseta\TransitoBundle\Entity\Accidente $accidente
     *
     * @return Juicio
     */
    public function setAccidente(\Buseta\TransitoBundle\Entity\Accidente $accidente = null)
    {
        $this->accidente = $accidente;

        return $this;
    }

    /**
     * Get accidente
     *
     * @return \Buseta\TransitoBundle\Entity\Accidente
     */
    public function getAccidente()
    {
        return $this->accidente;
    }

    /**
     * Set responsable
     *
     * @param string $responsable
     *
     * @return Juicio
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
     * @return Juicio
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return Juicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return Juicio
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Juicio
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
}

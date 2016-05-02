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
     * @ORM\Column(name="resultado", type="string")
     */
    protected $resultado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     * @Assert\Date()
     */
    private $fecha;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Juicio
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
}

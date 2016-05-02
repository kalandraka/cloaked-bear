<?php

namespace Buseta\TransitoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PenalAccidente
 *
 * @ORM\Table(name="t_penal_accidente")
 * @ORM\Entity(repositoryClass="Buseta\TransitoBundle\Entity\Repository\PenalAccidenteRepository")
 */
class PenalAccidente
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
     * @ORM\Column(name="nota", type="string")
     */
    protected $nota;

    /**
     * @var string
     *
     * @ORM\Column(name="adjunto", type="string")
     */
    protected $adjunto;

    /**
     * @var string
     *
     * @ORM\Column(name="alerta", type="string")
     */
    protected $alerta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     * @Assert\Date()
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaExpira", type="date")
     * @Assert\Date()
     */
    private $fechaExpira;

    /**
     * @var \Buseta\TransitoBundle\Entity\Accidente
     *
     * @ORM\ManyToOne(targetEntity="Buseta\TransitoBundle\Entity\Accidente")
     */
    private $accidente;

    /**
     * @var float
     *
     * @ORM\Column(name="costo", type="decimal", scale=2)
     */
    private $costo = 0;

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
     * Set nota
     *
     * @param string $nota
     *
     * @return PenalAccidente
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set adjunto
     *
     * @param string $adjunto
     *
     * @return PenalAccidente
     */
    public function setAdjunto($adjunto)
    {
        $this->adjunto = $adjunto;

        return $this;
    }

    /**
     * Get adjunto
     *
     * @return string
     */
    public function getAdjunto()
    {
        return $this->adjunto;
    }

    /**
     * Set alerta
     *
     * @param string $alerta
     *
     * @return PenalAccidente
     */
    public function setAlerta($alerta)
    {
        $this->alerta = $alerta;

        return $this;
    }

    /**
     * Get alerta
     *
     * @return string
     */
    public function getAlerta()
    {
        return $this->alerta;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return PenalAccidente
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
     * Set fechaExpira
     *
     * @param \DateTime $fechaExpira
     *
     * @return PenalAccidente
     */
    public function setFechaExpira($fechaExpira)
    {
        $this->fechaExpira = $fechaExpira;

        return $this;
    }

    /**
     * Get fechaExpira
     *
     * @return \DateTime
     */
    public function getFechaExpira()
    {
        return $this->fechaExpira;
    }

    /**
     * Set costo
     *
     * @param string $costo
     *
     * @return PenalAccidente
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;

        return $this;
    }

    /**
     * Get costo
     *
     * @return string
     */
    public function getCosto()
    {
        return $this->costo;
    }

    /**
     * Set accidente
     *
     * @param \Buseta\TransitoBundle\Entity\Accidente $accidente
     *
     * @return PenalAccidente
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

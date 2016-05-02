<?php

namespace Buseta\TransitoBundle\Form\Model;


use Buseta\TransitoBundle\Entity\Accidente;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AccidenteFilterModel.
 */
class AccidenteFilterModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var date
     */
    private $fechaInicio;

    /**
     * @var date
     */
    private $fechaFin;

    /**
     * @var \Buseta\BusesBundle\Entity\Vehiculo
     */
    private $vehiculo;

    /**
     * @var \Buseta\BusesBundle\Entity\Chofer
     */
    private $chofer;

    /**
     * Constructor
     */
    public function __construct(Accidente $entity = null)
    {
        if ($entity !== null) {
            $this->id = $entity->getId();

            $this->descripcion = $entity->getDescripcion();

            if ($entity->getVehiculo()) {
                $this->vehiculo  = $entity->getVehiculo();
            }
            if ($entity->getChofer()) {
                $this->chofer  = $entity->getChofer();
            }
        }
    }

    /**
     * @return Accidente
     */
    public function getEntityData()
    {
        $entity = new Accidente();

        $entity->setDescripcion($this->getDescripcion());

        if ($this->getVehiculo() !== null) {
            $entity->setVehiculo($this->getVehiculo());
        }
        if ($this->getChofer() !== null) {
            $entity->setChofer($this->getChofer());
        }

        return $entity;
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
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return date
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param date $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return date
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * @param date $fechaFin
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * @return \Buseta\BusesBundle\Entity\Vehiculo
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    /**
     * @param \Buseta\BusesBundle\Entity\Vehiculo $vehiculo
     */
    public function setVehiculo($vehiculo)
    {
        $this->vehiculo = $vehiculo;
    }

    /**
     * @return \Buseta\BusesBundle\Entity\Chofer
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * @param \Buseta\BusesBundle\Entity\Chofer $chofer
     */
    public function setChofer($chofer)
    {
        $this->chofer = $chofer;
    }

}

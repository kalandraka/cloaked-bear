<?php

namespace Buseta\TransitoBundle\Form\Model;


use Buseta\TransitoBundle\Entity\Multa;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MultaFilterModel.
 */
class MultaFilterModel
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
     * @var string
     */
    private $numArticulo;

    /**
     * @var date
     */
    private $fecha;

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
    public function __construct(Multa $multa = null)
    {
        if ($multa !== null) {
            $this->id = $multa->getId();

            $this->descripcion = $multa->getDescripcion();
            $this->numArticulo = $multa->getNumArticulo();
            $this->fecha = $multa->getFecha();

            if ($multa->getVehiculo()) {
                $this->vehiculo  = $multa->getVehiculo();
            }
            if ($multa->getChofer()) {
                $this->chofer  = $multa->getChofer();
            }
        }
    }

    /**
     * @return Multa
     */
    public function getEntityData()
    {
        $multa = new Multa();

        $multa->setDescripcion($this->getDescripcion());
        $multa->setNumArticulo($this->getNumArticulo());
        $multa->setFecha($this->getFecha());

        if ($this->getVehiculo() !== null) {
            $multa->setVehiculo($this->getVehiculo());
        }
        if ($this->getChofer() !== null) {
            $multa->setChofer($this->getChofer());
        }

        return $multa;
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
     * @return string
     */
    public function getNumArticulo()
    {
        return $this->numArticulo;
    }

    /**
     * @param string $numArticulo
     */
    public function setNumArticulo($numArticulo)
    {
        $this->numArticulo = $numArticulo;
    }

    /**
     * @return date
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param date $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
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

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
     * @var \Buseta\NomencladorBundle\Entity\Articulo
     */
    private $numArticulo;

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
    public function __construct(Multa $multa = null)
    {
        if ($multa !== null) {
            $this->id = $multa->getId();

            $this->descripcion = $multa->getDescripcion();
            $this->numArticulo = $multa->getNumArticulo();

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
     * @return \Buseta\NomencladorBundle\Entity\Articulo
     */
    public function getNumArticulo()
    {
        return $this->numArticulo;
    }

    /**
     * @param \Buseta\NomencladorBundle\Entity\Articulo $numArticulo
     */
    public function setNumArticulo($numArticulo)
    {
        $this->numArticulo = $numArticulo;
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

<?php

namespace Buseta\CombustibleBundle\Form\Model;

use Buseta\BusesBundle\Entity\Vehiculo;
use Buseta\CombustibleBundle\Entity\ServicioCombustible;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ServicioCombustibleFilterModel.
 */
class ServicioCombustibleFilterModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Buseta\CombustibleBundle\Entity\ConfiguracionCombustible
     */
    private $combustible;

    /**
     * @var string
     */
    private $estado = 'SV';

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
    public function __construct(ServicioCombustible $servicioCombustible = null)
    {
        if ($servicioCombustible !== null) {
            $this->id = $servicioCombustible->getId();

            if ($servicioCombustible->getVehiculo()) {
                $this->estadoCivil  = $servicioCombustible->getVehiculo();
            }
            if ($servicioCombustible->getChofer()) {
                $this->nacionalidad  = $servicioCombustible->getChofer();
            }
            if ($servicioCombustible->getCombustible()) {
                $this->nacionalidad  = $servicioCombustible->getCombustible();
            }
        }
    }

    /**
     * @return ServicioCombustible
     */
    public function getEntityData()
    {
        $servicioCombustible = new ServicioCombustible();

        $servicioCombustible->setEstado($this->getEstado());

        if ($this->getVehiculo() !== null) {
            $servicioCombustible->setVehiculo($this->getVehiculo());
        }
        if ($this->getChofer() !== null) {
            $servicioCombustible->setChofer($this->getChofer());
        }
        if ($this->getCombustible() !== null) {
            $servicioCombustible->setCombustible($this->getCombustible());
        }

        return $servicioCombustible;
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
     * @return \Buseta\CombustibleBundle\Entity\ConfiguracionCombustible
     */
    public function getCombustible()
    {
        return $this->combustible;
    }

    /**
     * @param \Buseta\CombustibleBundle\Entity\ConfiguracionCombustible $combustible
     */
    public function setCombustible($combustible)
    {
        $this->combustible = $combustible;
    }

    /**
     * @return Vehiculo
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    /**
     * @param Vehiculo $vehiculo
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
}

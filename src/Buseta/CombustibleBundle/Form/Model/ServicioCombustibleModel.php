<?php

namespace Buseta\CombustibleBundle\Form\Model;

use Buseta\BusesBundle\Entity\Chofer;
use Buseta\CombustibleBundle\Entity\ConfiguracionCombustible;
use Buseta\CombustibleBundle\Entity\ServicioCombustible;
use Doctrine\ORM\Mapping as ORM;
use Buseta\BusesBundle\Validator\Constraints as BusetaBusesAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\CombustibleBundle\Validator\Constraints as CombustibleAssert;
use Buseta\CombustibleBundle\Validator\Constraints\Marchamo2Valido;


/**
 * ServicioCombustibleModel
 *
 * @BusetaBusesAssert\CapacidadTanqueValido
 *
 * @CombustibleAssert\Marchamo1Valido()
 * @CombustibleAssert\Marchamo2Valido()
 */
class ServicioCombustibleModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var ConfiguracionCombustible
     */
    private $combustible;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     */
    private $cantidadLibros;

    /**
     * @var ChoferInServicioCombustible
     *
     * @Assert\Valid
     */
    private $chofer;

    /**
     * @var \Buseta\BusesBundle\Entity\Vehiculo
     *
     * @Assert\NotNull()
     */
    private $vehiculo;

    /**
     * @var string
     */
    private $boleta;

    /**
     * @var integer
     *
     * @Assert\NotBlank
     */
    private $marchamo1;

    /**
     * @var integer
     *
     * @Assert\NotBlank
     */
    private $marchamo2;

    /**
     * @return ServicioCombustible
     */
    public function getEntityData()
    {
        $servicioCombustible = new ServicioCombustible();
        $servicioCombustible->setCantidadLibros($this->getCantidadLibros());
        $servicioCombustible->setBoleta($this->getBoleta());
        $servicioCombustible->setMarchamo1($this->getMarchamo1());
        $servicioCombustible->setMarchamo2($this->getMarchamo2());

        if ($this->getCombustible() !== null) {
            $servicioCombustible->setCombustible($this->getCombustible());
        }
        if ($this->getChofer() !== null) {
            $servicioCombustible->setChofer($this->getChofer()->getChofer());
        }
        if ($this->getVehiculo() !== null) {
            $servicioCombustible->setVehiculo($this->getVehiculo());
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
     * @return ConfiguracionCombustible
     */
    public function getCombustible()
    {
        return $this->combustible;
    }

    /**
     * @param ConfiguracionCombustible $combustible
     */
    public function setCombustible(ConfiguracionCombustible $combustible)
    {
        $this->combustible = $combustible;
    }

    /**
     * @return int
     */
    public function getCantidadLibros()
    {
        return $this->cantidadLibros;
    }

    /**
     * @param int $cantidadLibros
     */
    public function setCantidadLibros($cantidadLibros)
    {
        $this->cantidadLibros = $cantidadLibros;
    }

    /**
     * @param mixed $chofer
     */
    public function setChofer($chofer)
    {
        if($chofer instanceof ChoferInServicioCombustible){
            $this->chofer = $chofer;
        }else if($chofer instanceof Chofer){
            $choferInServicioCombustible = new ChoferInServicioCombustible();
            $choferInServicioCombustible->setChofer($chofer);
            $this->chofer = $choferInServicioCombustible;
        }
    }

    /**
     * @return ChoferInServicioCombustible
     */
    public function getChofer()
    {
        return $this->chofer;
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
     * @return int
     */
    public function getMarchamo1()
    {
        return $this->marchamo1;
    }

    /**
     * @param int $marchamo1
     */
    public function setMarchamo1($marchamo1)
    {
        $this->marchamo1 = $marchamo1;
    }

    /**
     * @return int
     */
    public function getMarchamo2()
    {
        return $this->marchamo2;
    }

    /**
     * @param int $marchamo2
     */
    public function setMarchamo2($marchamo2)
    {
        $this->marchamo2 = $marchamo2;
    }

    /**
     * @param string $boleta
     */
    public function setBoleta($boleta)
    {
        $this->boleta = $boleta;
    }

    /**
     * @return string
     */
    public function getBoleta()
    {
        return $this->boleta;
    }
}

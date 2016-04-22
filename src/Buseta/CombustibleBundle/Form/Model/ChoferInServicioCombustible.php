<?php

namespace Buseta\CombustibleBundle\Form\Model;

use Buseta\BusesBundle\Entity\Chofer;
use Buseta\CoreBundle\Validator\Constraints as BusetaCoreAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChoferInServicioCombustible
 * @package Buseta\CoreBundle\Form\Models
 * @BusetaCoreAssert\CodigoYPinValido
 */
class ChoferInServicioCombustible {
    /**
     * @var \Buseta\BusesBundle\Entity\Chofer
     * @Assert\NotNull()
     */
    private $chofer;

    /**
     * @var string
     */
    private $codigobarras;

    /**
     * @var string
     * @Assert\NotBlank
     */
    private $pin;

    /**
     * @param mixed $chofer
     */
    public function setChofer($chofer)
    {
        $this->chofer = $chofer;
    }

    /**
     * @return \Buseta\BusesBundle\Entity\Chofer
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * @param mixed $codigobarras
     */
    public function setCodigobarras($codigobarras)
    {
        $this->codigobarras = $codigobarras;
    }

    /**
     * @return mixed
     */
    public function getCodigobarras()
    {
        return $this->codigobarras;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }


}

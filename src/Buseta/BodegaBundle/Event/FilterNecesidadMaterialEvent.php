<?php

namespace Buseta\BodegaBundle\Event;

use Buseta\BodegaBundle\Entity\NecesidadMaterial;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FilterNecesidadMaterialEvent
 *
 * @package Buseta\BodegaBundle\Event
 */
class FilterNecesidadMaterialEvent extends Event
{
    /**
     * @var NecesidadMaterial
     */
    private $necesidadMaterial;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @var boolean
     */
    private $flush;

    /**
     * FilterNecesidadMaterialEvent constructor
     *
     * @param NecesidadMaterial $necesidadMaterial
     * @param boolean $flush
     */
    function __construct(NecesidadMaterial $necesidadMaterial, $flush=true)
    {
        $this->necesidadMaterial = $necesidadMaterial;
        $this->error = false;
        $this->flush = $flush;
    }

    /**
     * @return NecesidadMaterial
     */
    public function getNecesidadMaterial()
    {
        return $this->necesidadMaterial;
    }

    /**
     * @param NecesidadMaterial $necesidadMaterial
     */
    public function setNecesidadMaterial($necesidadMaterial)
    {
        $this->necesidadMaterial = $necesidadMaterial;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return boolean
     */
    public function isFlush()
    {
        return $this->flush;
    }
}

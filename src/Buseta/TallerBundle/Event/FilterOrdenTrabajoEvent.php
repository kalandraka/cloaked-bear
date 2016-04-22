<?php
/**
 * Created by PhpStorm.
 * User: anierm
 * Date: 22/12/15
 * Time: 21:30
 */

namespace Buseta\TallerBundle\Event;

use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Symfony\Component\EventDispatcher\Event;

class FilterOrdenTrabajoEvent extends Event
{
    /**
     * @var \Buseta\TallerBundle\Entity\OrdenTrabajo
     */
    private $orden;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @param $orden
     */
    function __construct(OrdenTrabajo $orden)
    {
        $this->orden = $orden;
    }

    /**
     * @return \Buseta\TallerBundle\Entity\OrdenTrabajo
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     */
    public function setOrden(OrdenTrabajo $orden)
    {
        $this->orden = $orden;
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
}
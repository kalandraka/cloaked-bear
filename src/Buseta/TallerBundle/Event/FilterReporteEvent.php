<?php

namespace Buseta\TallerBundle\Event;

use Buseta\TallerBundle\Entity\Reporte;
use Symfony\Component\EventDispatcher\Event;

class FilterReporteEvent extends Event
{
    /**
     * @var \Buseta\TallerBundle\Entity\Reporte
     */
    private $reporte;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @param $reporte
     */
    function __construct(Reporte $reporte)
    {
        $this->reporte = $reporte;
    }

    /**
     * @return \Buseta\TallerBundle\Entity\Reporte
     */
    public function getReporte()
    {
        return $this->reporte;
    }

    /**
     */
    public function setReporte(Reporte $reporte)
    {
        $this->reporte=$reporte;
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

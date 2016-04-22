<?php

namespace Buseta\TallerBundle\Event;

use Buseta\TallerBundle\Entity\Diagnostico;
use Symfony\Component\EventDispatcher\Event;

class FilterDiagnosticoEvent extends Event
{
    /**
     * @var \Buseta\TallerBundle\Entity\Diagnostico
     */
    private $diagnostico;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @param $diagnostico
     */
    function __construct(Diagnostico $diagnostico)
    {
        $this->diagnostico = $diagnostico;
    }

    /**
     * @return \Buseta\TallerBundle\Entity\Diagnostico
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     */
    public function setDiagnostico(Diagnostico $diagnostico)
    {
        $this->diagnostico = $diagnostico;
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
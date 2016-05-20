<?php

namespace HatueySoft\DateTimeBundle\Twig;

use HatueySoft\DateTimeBundle\Managers\FechaSistemaManager;

class FechaSistemaExtension extends \Twig_Extension
{
    private $fechaSistemaManager;

    function __construct(FechaSistemaManager $fechaSistemaManager)
    {
        $this->fechaSistemaManager = $fechaSistemaManager;
    }

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('fecha_sistema_activa', array($this, 'showFechaSistema'), array('is_safe' => array('html'))),
        );
    }

    public function showFechaSistema()
    {
        if($this->fechaSistemaManager->isActive()) {
            return sprintf(
                '<div class="dateChangeAlert">%s "%s"</div>',
                strtoupper('Activa fecha del sistema'),
                date_format($this->fechaSistemaManager->getFechaSistema(), 'd/m/Y')
            );
        }
    }

    public function getName()
    {
        return 'fecha_sistema_extension';
    }
}

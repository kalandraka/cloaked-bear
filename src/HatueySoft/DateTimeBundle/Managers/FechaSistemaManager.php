<?php

namespace HatueySoft\DateTimeBundle\Managers;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Util\StringUtils;

/**
 * Class FechaSistemaManager
 *
 * @package HatueySoft\DateTimeBundle\Managers
 */
class FechaSistemaManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CambioHoraSistemaManager
     */
    private $cambioHoraManager;

    function __construct(EntityManager $em, CambioHoraSistemaManager $cambioHoraManager)
    {
        $this->em = $em;
        $this->cambioHoraManager = $cambioHoraManager;
    }

    /**
     * @return \DateTime
     */
    public function getFechaSistema()
    {
        //comprobando si existe fecha de sistema activa
        $fechaSistemaConfig = $this->em->getRepository('HatueySoftDateTimeBundle:FechaSistema')
            ->findOneBy(array('activo', true));

        if ($fechaSistemaConfig !== null) {
            $fechaSistema = $fechaSistemaConfig->getFecha();
        } else {
            $fechaSistema = new \DateTime();
        }

        return $fechaSistema;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        //comprobando si existe fecha de sistema activa
        $fechaSistemaConfig = $this->em->getRepository('HatueySoftDateTimeBundle:FechaSistema')->findAll();
        if (count($fechaSistemaConfig) == 1) {
            $fechaSistemaConfig = $fechaSistemaConfig[0];
            if ($fechaSistemaConfig->getActivo()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    public function isTodayDate(\DateTime $date)
    {
        $fechaSistema = $this->getFechaSistema();

        $dateString = date_format($date, 'Y-m-d');
        $fechaSistemaString = date_format($fechaSistema, 'Y-m-d');

        return StringUtils::equals($dateString, $fechaSistemaString);
    }
}

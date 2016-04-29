<?php

namespace HatueySoft\DateTimeBundle\Managers;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Util\StringUtils;

/**
 * Class FechaSistemaManager
 *
 * @package HatueySoft\DateTimeBundle\Managers
 */
class FechaSistemaManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var HoraSistemaManager
     */
    private $cambioHoraManager;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;


    /**
     * FechaSistemaManager constructor.
     *
     * @param EntityManager         $em
     * @param HoraSistemaManager    $cambioHoraManager
     * @param TokenStorageInterface $tokenStorage
     */
    function __construct(EntityManager $em, HoraSistemaManager $cambioHoraManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->cambioHoraManager = $cambioHoraManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get System Date and Time
     *
     * @return \DateTime
     */
    public function getFechaSistema()
    {
        $username = $this->tokenStorage->getToken()->getUsername();

        //comprobando si existe fecha de sistema activa
        $fechaSistemaConfig = $this->em->getRepository('HatueySoftDateTimeBundle:FechaSistema')
            ->getUserConfig($username);
        if ($fechaSistemaConfig && $fechaSistemaConfig->getActivo()) {
            $fechaSistema = $fechaSistemaConfig->getFecha();
        } else {
            $fechaSistema = new \DateTime();
        }

        return $fechaSistema;
    }

    /**
     * Check if any system date is active for the current user.
     *
     * @return bool
     */
    public function isActive()
    {
        $username = $this->tokenStorage->getToken()->getUsername();

        $fechaSistemaConfig = $this->em->getRepository('HatueySoftDateTimeBundle:FechaSistema')
            ->getUserConfig($username);
        if ($fechaSistemaConfig && $fechaSistemaConfig->getActivo()) {
            return true;
        }

        return false;
    }

    /**
     * Compare if date is the same as system date.
     *
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

    /**
     * Returns active system date for the user, false otherwise
     *
     * @return \HatueySoft\DateTimeBundle\Entity\FechaSistema | bool
     */
    public function getUserConfig()
    {
        $username = $this->tokenStorage->getToken()->getUsername();

        return $this->em->getRepository('HatueySoftDateTimeBundle:FechaSistema')
            ->getUserConfig($username);
    }
}

<?php

namespace HatueySoft\DateTimeBundle\Managers;

use Doctrine\ORM\EntityManager;
use HatueySoft\DateTimeBundle\Entity\FechaSistema;
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
        if ($fechaSistemaConfig && $fechaSistemaConfig->isActivo()) {
            $fechaSistema = $fechaSistemaConfig->getFecha();
        } else {
            $fechaSistema = new \DateTime();
        }

        return $fechaSistema;
    }

    public function setFechaSistema(\DateTime $date = null)
    {
        $username = $this->tokenStorage->getToken()->getUsername();
        $fechaSistemaConfig = $this->em->getRepository('HatueySoftDateTimeBundle:FechaSistema')
            ->getUserConfig($username);

        if ($fechaSistemaConfig !== null && $fechaSistemaConfig !== false) {
            if ($date === null) {
                $fechaSistemaConfig->setActivo(false);
                $fechaSistemaConfig->setFecha(null);
            } else {
                $fechaSistemaConfig->setActivo(true);
                $fechaSistemaConfig->setFecha($date);
            }

            $this->em->flush();

            return $fechaSistemaConfig;
        } elseif ($date !== null) {
            $fechaSistemaConfig = new FechaSistema();
            $fechaSistemaConfig->setActivo(true);
            $fechaSistemaConfig->setFecha($date);
            $fechaSistemaConfig->setUsername($username);

            $this->em->persist($fechaSistemaConfig);
            $this->em->flush();

            return $fechaSistemaConfig;
        }

        return false;
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

<?php

namespace HatueySoft\DateTimeBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Class FechaSistemaRepository
 *
 * @package HatueySoft\DateTimeBundle\Entity\Repository
 */
class FechaSistemaRepository extends EntityRepository
{
    /**
     * Returns username system date config.
     *
     * @param $username
     * @return \HatueySoft\DateTimeBundle\Entity\FechaSistema | bool
     */
    public function getUserConfig($username)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('config')
            ->from('HatueySoftDateTimeBundle:FechaSistema', 'config')
            ->where($qb->expr()->eq('config.username', ':username'))
            ->setParameters(array(
                'username' => $username,
            ))
            ->getQuery();

        try {
            return $query->getSingleResult();
        } catch (NoResultException $nre) {
        } catch (NonUniqueResultException $nure) {
        }

        return false;
    }
}

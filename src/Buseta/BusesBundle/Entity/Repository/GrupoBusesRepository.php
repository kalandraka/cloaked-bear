<?php

namespace Buseta\BusesBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Buseta\BusesBundle\Form\Model\GrupoBusesFilterModel;
/**
 * GrupoBusesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GrupoBusesRepository extends EntityRepository
{
    public function filter(GrupoBusesFilterModel $filter = null)
    {
        $qb = $this->createQueryBuilder('gb');
        $query = $qb->where($qb->expr()->eq(true,true));

        if($filter) {
            if ($filter->getNombre() !== null && $filter->getNombre() !== '') {
                $query->andWhere($qb->expr()->like('gb.nombre',':nombre'))
                    ->setParameter('nombre', '%' . $filter->getNombre() . '%');
            }
            if ($filter->getColor() !== null && $filter->getColor() !== '') {
                $query->andWhere($qb->expr()->like('gb.color',':color'))
                    ->setParameter('color', '%' . $filter->getColor() . '%');
            }

        }

        $query->orderBy('gb.id', 'ASC');

        try {
            return $query->getQuery();
        } catch (NoResultException $e) {
            return array();
        }
    }

}

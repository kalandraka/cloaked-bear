<?php

namespace Buseta\TransitoBundle\Entity\Repository;

use Buseta\TransitoBundle\Form\Model\MultaFilterModel;

/**
 * MultaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MultaRepository extends \Doctrine\ORM\EntityRepository
{
    public function filter(MultaFilterModel $filter = null)
    {
        $qb = $this->createQueryBuilder('m');
        $query = $qb->where($qb->expr()->eq(true,true));

        if($filter) {
            if ($filter->getFechaInicio() !== null && $filter->getFechaInicio() !== '') {
                $query->andWhere($qb->expr()->gte('m.fecha',':fechaInicio'))
                    ->setParameter('fechaInicio', $filter->getFechaInicio());
            }
            if ($filter->getFechaFin() !== null && $filter->getFechaFin() !== '') {
                $query->andWhere($qb->expr()->lte('m.fecha',':fechaFin'))
                    ->setParameter('fechaFin', $filter->getFechaFin());
            }
            if ($filter->getDescripcion() !== null && $filter->getDescripcion() !== '') {
                $query->andWhere($qb->expr()->like('m.descripcion',':descripcion'))
                    ->setParameter('descripcion', '%' . $filter->getDescripcion() . '%');
            }
            if ($filter->getNumArticulo() !== null && $filter->getNumArticulo() !== '') {
                $query->andWhere($query->expr()->eq('m.numArticulo', ':numArticulo'))
                    ->setParameter('numArticulo', $filter->getNumArticulo());
            }
            if ($filter->getVehiculo() !== null && $filter->getVehiculo() !== '') {
                $query->andWhere($query->expr()->eq('m.vehiculo', ':vehiculo'))
                    ->setParameter('vehiculo', $filter->getVehiculo());
            }
            if ($filter->getChofer() !== null && $filter->getChofer() !== '') {
                $query->andWhere($query->expr()->eq('m.chofer', ':chofer'))
                    ->setParameter('chofer', $filter->getChofer());
            }
        }

        $query->orderBy('m.id', 'DESC');

        try {
            return $query->getQuery();
        } catch (NoResultException $e) {
            return array();
        }
    }
}

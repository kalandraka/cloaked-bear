<?php

namespace Buseta\BodegaBundle\Entity\Repository;

use Buseta\BodegaBundle\Entity\CategoriaProducto;
use Buseta\BodegaBundle\Form\Model\CategoriaProductoFilterModel;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * CategoriaProductoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriaProductoRepository extends EntityRepository
{
    public function filter(CategoriaProductoFilterModel $filter = null)
    {
        $qb = $this->createQueryBuilder('c');
        $query = $qb->where($qb->expr()->eq(true,true));

        if($filter) {
            if ($filter->getValor() !== null && $filter->getValor() !== '') {
                $query->andWhere($qb->expr()->like('c.valor',':valor'))
                    ->setParameter('valor', '%' . $filter->getValor() . '%');
            }
        }

        $query->orderBy('c.id', 'ASC');

        try {
            return $query->getQuery();
        } catch (NoResultException $e) {
            return array();
        }
    }

    public function searchByValor($valores) {
        $q = "SELECT r FROM BusetaBodegaBundle:CategoriaProducto r WHERE r.id = :valores";

        $query = $this->_em->createQuery($q);
        $query->setParameter('valores', $valores);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return false;
        }
    }
}

<?php

namespace Buseta\TallerBundle\Entity\Repository;

use Buseta\TallerBundle\Form\Model\OrdenTrabajoFilterModel;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * OrdenTrabajoRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrdenTrabajoRepository extends EntityRepository
{
    public function filter(OrdenTrabajoFilterModel $filter = null)
    {
        $qb = $this->createQueryBuilder('o');
        $query = $qb->where($qb->expr()->eq(true,true));

        if($filter) {
            if ($filter->getNumero() !== null && $filter->getNumero() !== '') {
                $query->andWhere($qb->expr()->like('o.numero',':numero'))
                    ->setParameter('numero', '%' . $filter->getNumero() . '%');
            }
            if ($filter->getRequisionMateriales() !== null && $filter->getRequisionMateriales() !== '') {
                $query->andWhere($qb->expr()->like('o.requisionMateriales',':requisionMateriales'))
                    ->setParameter('requisionMateriales', '%' . $filter->getRequisionMateriales() . '%');
            }
            if ($filter->getDiagnosticadoPor() !== null && $filter->getDiagnosticadoPor() !== '') {
                $query->andWhere($query->expr()->eq('o.diagnosticadoPor', ':diagnosticadoPor'))
                    ->setParameter('diagnosticadoPor', $filter->getDiagnosticadoPor());
            }
            if ($filter->getAyudante() !== null && $filter->getAyudante() !== '') {
                $query->andWhere($query->expr()->eq('o.ayudante', ':ayudante'))
                    ->setParameter('ayudante', $filter->getAyudante());
            }
            if ($filter->getAutobus() !== null && $filter->getAutobus() !== '') {
                $query->andWhere($query->expr()->eq('o.autobus', ':autobus'))
                    ->setParameter('autobus', $filter->getAutobus());
            }
            if ($filter->getDiagnostico() !== null && $filter->getDiagnostico() !== '') {
                $query->andWhere($query->expr()->eq('o.diagnostico', ':diagnostico'))
                    ->setParameter('diagnostico', $filter->getDiagnostico());
            }

        }

        $query->orderBy('o.id', 'DESC');

        return $query->getQuery();
    }
}

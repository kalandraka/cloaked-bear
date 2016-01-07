<?php

namespace Buseta\TallerBundle\Entity\Repository;

use Buseta\TallerBundle\Form\Model\ReporteFilterModel;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * ReporteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReporteRepository extends EntityRepository
{
    public function filter($status = null, ReporteFilterModel $filter = null)
    {
        $qb = $this->createQueryBuilder('r');
        $query = $qb->where($qb->expr()->eq(true,true));


        if($filter) {
            if ($filter->getNumero() !== null && $filter->getNumero() !== '') {
                $query->andWhere($query->expr()->like('r.numero',':numero'))
                    ->setParameter('numero',  sprintf('%%%s%%', $filter->getNumero()));
            }
            if ($filter->getAutobus() !== null && $filter->getAutobus() !== '') {
                $query->andWhere($query->expr()->eq('r.autobus', ':autobus'))
                    ->setParameter('autobus', $filter->getAutobus());
            }
            if ($filter->getFechaInicio() !== null && $filter->getFechaInicio() !== '') {
                $fechaInicio = date_create_from_format('Y-m-d H:i:s', sprintf('%s 00:00:00', $filter->getFechaInicio()->format('Y-m-d')));
                $query->andWhere($query->expr()->gte('r.created', ':fechaInicio'))
                    ->setParameter('fechaInicio', $fechaInicio);
            }
            if ($filter->getFechaFin() !== null && $filter->getFechaFin() !== '') {
                $fechaFin = date_create_from_format('Y-m-d H:i:s', sprintf('%s 23:59:59', $filter->getFechaFin()->format('Y-m-d')));
                $query->andWhere($query->expr()->lte('r.created', ':fechaFin'))
                    ->setParameter('fechaFin', $fechaFin);
            }
        } else if ($status !== null && $status !== '') {
            if ($status === 'CO') {
                $query->andWhere(
                    $query->expr()->orX(
                        $query->expr()->eq('r.estado', ':completado'),
                        $query->expr()->eq('r.estado', ':cerrado')
                    )
                )
                    ->setParameters(array('completado' => 'CO', 'cerrado' => 'CL'));
            } else {
                $query->andWhere($query->expr()->eq('r.estado', ':estado'))
                    ->setParameter('estado', $status);
            }
        }

        $query->orderBy('r.created', 'DESC');

        try {
            return $query->getQuery();
        } catch (NoResultException $e) {
            return array();
        }
    }

    /**
     * Devuelve un array con el total de elementos y el total de elementos atrasados
     * de un estado pasado por parametro
     *
     * @param $estado El estado del reporte(solicitud)
     *
     * @return Array (['total']  ['atrasados'])
     */
    public function findTotalAtrasadas($estado = null)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT   r.created AS fechahoracreado, r.estado, tp.minutos
            FROM     BusetaTallerBundle:Reporte r LEFT JOIN r.prioridad p LEFT JOIN p.tiempoPrioridad tp
            WHERE      r.estado = :estado');
        $consulta->setParameter('estado', $estado);

        $filas =  $consulta->getArrayResult();
        $num_atrasadas = 0;

        foreach ($filas as $fila) {
            $minutos     = $fila['minutos'];
            $fechacreado = $fila['fechahoracreado'];
            if (($minutos != null) && ($minutos != null))  {
                $fechavence  = $fechacreado->modify('+'.$minutos.' minutes');
                $fechaahora  = new \DateTime('now');

                if ($fechavence < $fechaahora ) {
                    $num_atrasadas++;
                }
            }
        }

        //para devolver el total de filas
        $valor['total']= count($filas);
        //para devolver el total de atrasadas
        $valor['atrasados']=   $estado != 'CO' ? $num_atrasadas : 0;

        return $valor ;
    }

    /**
     * Devuelve un array con el total de elementos y el total de elementos atrasados
     * de un array de entidades pasado por parametro desde la consulta de filtro del formulario
     *
     * @param $estado El estado del reporte(solicitud)
     *
     * @return Array (['total']  ['atrasados'])
     */
    public function findTotalAtrasadasFilter($entities = null)
    {

        $num_atrasadas = 0;

        foreach ($entities as $entity) {
            //si ocurre error no se cuenta como atrasada
            try {
                if ($entity->getPrioridad() !== null && $entity->getPrioridad()->getTiempoPrioridad() !== null) {
                    $minutos = $entity->getPrioridad()->getTiempoPrioridad()->getMinutos();
                    $fechacreado = $entity->getCreated();

                    if ($minutos !== null) {
                        $fechavence = $fechacreado->modify('+' . $minutos . ' minutes');
                        $fechaahora = new \DateTime('now');
                        if (($fechavence < $fechaahora) && ($entity->getEstado() != 'CO')) {
                            $num_atrasadas++;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        //para devolver el total de filas
        $valor['total']= count($entities);
        //para devolver el total de atrasadas
        $valor['atrasados']= $num_atrasadas ;

        return $valor ;
    }


}

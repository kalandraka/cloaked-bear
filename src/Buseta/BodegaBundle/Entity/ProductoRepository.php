<?php

namespace Buseta\BodegaBundle\Entity;

use Buseta\NomencladorBundle\Entity\Categoria;
use Doctrine\ORM\EntityRepository;

/**
 * ProductoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductoRepository extends EntityRepository
{
    public function searchByValor($valores) {
        $q = "SELECT r FROM BusetaBodegaBundle:Producto r WHERE r.id = :valores";

        $query = $this->_em->createQuery($q);
        $query->setParameter('valores', $valores);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return false;
        }
    }

    public function comprobarInformeStock($almacen,$producto,$em)
    {
        $entities = $em->createQueryBuilder()
            ->select('o')
            ->from('BusetaBodegaBundle:InformeStock', 'o');

        if($almacen){
            $entities
                ->where('o.almacen = :almacen');
            $entities
                ->setParameter('almacen', $almacen);
        }

        if($producto){
            $entities
                ->andWhere('o.producto = :producto');
            $entities
                ->setParameter('producto', $producto);
        }

        $entities = $entities
            ->getQuery();

        return $entities;

    }

    public function informeStock($busqueda,$em)
    {
        $entities = $em->createQueryBuilder()
            ->select('p')
            ->from('BusetaBodegaBundle:Producto','p');

        $datos = $busqueda->getData();

        if($datos->getProducto()){
            $entities
                ->where('p.id = :producto');
            $entities
                ->setParameter('producto', $datos->getProducto());
        }


        if($datos->getCategoria()){
            $entities->andWhere('c.id = :categoria');
            $entities->leftJoin('p.categoria', 'c');
            $entities->setParameter('categoria', $datos->getCategoria()->getId());
        }

        $entities = $entities
            ->getQuery();

        return $entities;
    }

    public function buscarTodos($em)
    {
        $entities = $em->createQueryBuilder()
            ->select('p')
            ->from('BusetaBodegaBundle:Producto','p');

        $entities = $entities
            ->getQuery();

        return $entities;
    }

    public function busquedaAvanzada($page, $cantResult, $filter = array(), $orderBy = null) {
        $q = 'SELECT p FROM BusetaBodegaBundle:Producto p WHERE p.id != 0';

        //Obteniendo resto de la consulta dql
        $q.=$this->constructSubDQL($filter);

        //Estableciendo Order By
        if (empty($orderBy))
            $q.=' ORDER BY p.id DESC';
        else
            $q.= ' ORDER BY ' . $orderBy;

        $maxResult = $this->getNotDeletedMaxResult($filter);
        $firstResult = $page * $cantResult;

        if ($firstResult > $maxResult) {
            $firstResult = 0;
            $page = 0;
        }

        //Valores de navegación
        if($maxResult < $cantResult)
            $last = 0;
        elseif ($maxResult % $cantResult > 0)
            $last = floor($maxResult / $cantResult);
        else
            $last = floor($maxResult / $cantResult)-1;


        if($last < 0)
            $last = 0;
        $next = ($page != $last) ? true : false;
        $prev = ($page != 0) ? true : false;
        $first = ($page == 0) ? false : true;

        $query = $this->_em->createQuery($q);
        $query->setMaxResults($cantResult);
        $query->setFirstResult($firstResult);

        try {
            $results = $query->getResult();
            return array(
                'results' => $results,
                'paginacion' => array(
                    'next' => $next,
                    'prev' => $prev,
                    'first' => $first,
                    'last' => $last,
                ),
            );
        } catch (NoResultException $e) {
            return array(
                'results' => array(),
                'paginacion' => array(),
            );
        }
    }

    public function getNotDeletedMaxResult($filter) {
        $q = 'SELECT COUNT(p) FROM BusetaBodegaBundle:Producto p WHERE p.id != 0';
        $q.=$this->constructSubDQL($filter);

        $query = $this->_em->createQuery($q);
        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return false;
        }
    }

    public function constructSubDQL($filter) {
        $q = '';

        if (isset($filter['uom']) && !empty($filter['uom'])){

            $results = $this->_em->getRepository('BusetaNomencladorBundle:UOM')->searchByValor($filter['uom']);

            if(count($results) !=0)
                $q.= " AND p.uom = " . $results[0]->getId() . " ";
            else
                $q.= " AND p.uom = -1 ";
        }

        if (isset($filter['condicion']) && !empty($filter['condicion'])){

            $results = $this->_em->getRepository('BusetaNomencladorBundle:Condicion')->searchByValor($filter['condicion']);

            if(count($results) !=0)
                $q.= " AND p.condicion = " . $results[0]->getId() . " ";
            else
                $q.= " AND p.condicion = -1 ";
        }

        if (isset($filter['bodega']) && !empty($filter['bodega'])){

            $results = $this->_em->getRepository('BusetaBodegaBundle:Bodega')->searchByValor($filter['bodega']);

            if(count($results) !=0)
                $q.= " AND p.bodega = " . $results[0]->getId() . " ";
            else
                $q.= " AND p.bodega = -1 ";
        }

        if (isset($filter['categoriaProducto']) && !empty($filter['categoriaProducto'])){

            $results = $this->_em->getRepository('BusetaBodegaBundle:CategoriaProducto')->searchByValor($filter['categoriaProducto']);

            if(count($results) !=0)
                $q.= " AND p.categoriaProducto = " . $results[0]->getId() . " ";
            else
                $q.= " AND p.categoriaProducto = -1 ";
        }

        if (isset($filter['codigo']) && !empty($filter['codigo']))
            $q.= " AND UPPER(p.codigo) LIKE '%" . strtoupper($filter['codigo']) . "%'";

        if (isset($filter['nombre']) && !empty($filter['nombre']))
            $q.= " AND UPPER(p.nombre) LIKE '%" . strtoupper($filter['nombre']) . "%'";

        if (isset($filter['apellidos']) && !empty($filter['apellidos']))
            $q.= " AND UPPER(p.apellidos) LIKE '%" . strtoupper($filter['apellidos']) . "%'";

        if (isset($filter['alias']) && !empty($filter['alias']))
            $q.= " AND UPPER(p.alias) LIKE '%" . strtoupper($filter['alias']) . "%'";


        return $q;
    }

}

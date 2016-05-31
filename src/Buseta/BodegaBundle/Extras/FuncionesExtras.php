<?php

namespace Buseta\BodegaBundle\Extras;

use Buseta\BodegaBundle\BusetaBodegaMovementTypes;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FuncionesExtras
{
    public function ImporteLinea($cantidad_pedido, $precio_unitario, $impuesto = null, $porciento_descuento = 0)
    {
        $importeBruto = $cantidad_pedido * $precio_unitario;
        $importeDescuento = $importeBruto * $porciento_descuento / 100;
        $importeImpuesto = 0;

        if ($impuesto) {
            $tipoImpuesto = $impuesto->getTipo();
            $tarifaImpuesto = $impuesto->getTarifa();

            if ($tipoImpuesto == "fijo") {
                $importeImpuesto = $tarifaImpuesto;
            } elseif ($tipoImpuesto == "porcentaje") {
                $importeImpuesto = $importeBruto * $tarifaImpuesto / 100;
            }
        }

        return $importeBruto + $importeImpuesto - $importeDescuento;
    }


    //PREGUNTAR A CARLOS !!!!, ya veo este no se usa ya
    //Creo que aqui es Para 1) todos los almacenes y 2) todos los productos , me fijo entonces en todas las bitácoras
    //donde este ese producto en ese almacen, y encuentro la cantidad teorica disponible,
    //y para los que tengan existencia(cantidad >0), entonces actualizo el stock, si no esta lo crea
    public function ActualizarInformeStock($busqueda, EntityManager $em)
    {
        $almacenes = $em->getRepository('BusetaBodegaBundle:Bodega')->findAll();
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')->findAll();

        $datos = $busqueda->getData();

        //Recorro cada almacen
        foreach ($almacenes as $almacen) {

            //Obtengo la bitacora para el almacen actual
            $bitacoras = $em->getRepository('BusetaBodegaBundle:InformeStock')->buscarAlmacenBitacora(
                $almacen,
                $datos['fecha']
            );

            $cantidadPedido = 0;

            //Busco cada producto existente para la bitacora actual
            foreach ($productos as $producto) {
                foreach ($bitacoras as $bitacora) {
                    /** @var \Buseta\BodegaBundle\Entity\BitacoraAlmacen $bitacora */
                    if ($producto == $bitacora->getProducto()) {
                        //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
                        if (self::movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido += $bitacora->getCantidadMovida();
                        }
                        if (self::movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido -= $bitacora->getCantidadMovida();
                        }
                    }
                }

                //Actualizo el informeStock
                if ($cantidadPedido != 0) {
                    $informeStockExistente = $em->getRepository('BusetaBodegaBundle:InformeStock')->findOneBy(
                        array(
                            'almacen' => $almacen,
                            'producto' => $producto,
                        )
                    );

                    if ($informeStockExistente) {
                        $informeStockExistente->setCantidadProductos($cantidadPedido);
                        $em->persist($informeStockExistente);
                        $em->flush();
                    } else {
                        $informeStock = new InformeStock();
                        $informeStock->setProducto($producto);
                        $informeStock->setAlmacen($almacen);
                        $informeStock->setCantidadProductos($cantidadPedido);
                        $em->persist($informeStock);
                        $em->flush();
                    }
                }

                //Reinicio cantidad de pedidos
                $cantidadPedido = 0;
            }
        }
    }


    //HACE LO MISMO QUE EL DE ARRIBA , pero aqui debería consultar la tabla (BusetaBodegaBundle:InformeStock)
    //pues esa tabla tiene todo lo necesario, si es necesario el algoritmo es idem al de arriba
    public function generarInformeStockLegacy($bitacoras, EntityManager $em)
    {
        $almacenes = $em->getRepository('BusetaBodegaBundle:Bodega')->findAll();
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')->findAll();

        $almacenesArray = array(array());
        $pos = 0;
        $almacenesArray[$pos]['almacen'] = null;
        $almacenesArray[$pos]['producto'] = null;
        $almacenesArray[$pos]['cantidad'] = null;

        //Recorro cada almacen
        foreach ($almacenes as $almacen) {
            $cantidadPedido = 0;

            //Busco cada producto existente para la bitacora actual
            foreach ($productos as $producto) {
                foreach ($bitacoras as $bitacora) {
                    /** @var \Buseta\BodegaBundle\Entity\BitacoraAlmacen $bitacora */
                    if ($producto == $bitacora->getProducto() && $bitacora->getAlmacen() == $almacen) {
                        //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
                        if (self::movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido += $bitacora->getCantidadMovida();
                        }
                        if (self::movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido -= $bitacora->getCantidadMovida();
                        }
                    }
                }

                //Actualizo el informeStock
                if ($cantidadPedido != 0) {
                    $almacenesArray[$pos]['almacen'] = $almacen;
                    $almacenesArray[$pos]['producto'] = $producto;
                    $almacenesArray[$pos]['cantidad'] = $cantidadPedido;
                    $pos += 1;
                }

                //Reinicio cantidad de pedidos
                $cantidadPedido = 0;
            }
        }

        return $almacenesArray;
    }

    public function generarInformeStock($filter, EntityManager $em)
    {
        $qb = $em->createQueryBuilder();
        $qb->select('product.id as producto_id,
            product.nombre as producto_nombre,
            product.tieneNroSerie as producto_seriado,
            warehouse.id as almacen_id,
            warehouse.nombre as almacen_nombre,
            SUM(bitacora.cantidad) as cant
        ')
            ->from('BusetaBodegaBundle:BitacoraAlmacen', 'bitacora')
            ->innerJoin('bitacora.producto', 'product')
            ->innerJoin('bitacora.almacen', 'warehouse')
            ->groupBy('bitacora.almacen, bitacora.producto')
            ->having('cant > 0');

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if (isset($filter['almacen']) && $filter['almacen'] !== null) {
            $qb->andWhere($qb->expr()->eq('warehouse.id', ':almacen'))
            ->setParameter('almacen', $propertyAccessor->getValue($filter['almacen'], 'id'));
        }
        if (isset($filter['categoriaProducto']) && $filter['categoriaProducto'] !== null) {
            $qb->andWhere($qb->expr()->eq('product.categoriaProducto', ':categoriaProducto'))
                ->setParameter('categoriaProducto', $propertyAccessor->getValue($filter['categoriaProducto'], 'id'));
        }
        if (isset($filter['fecha']) && $filter['fecha'] !== null) {
            $qb->andWhere($qb->expr()->lte('bitacora.fechaMovimiento', ':fechaMovimiento'))
                ->setParameter('fechaMovimiento', date_format($filter['fecha'], 'Y-m-d'));
        }

        try {
            return $qb->getQuery()
                ->getArrayResult();
        } catch (NoResultException $e) {
            return array();
        }
    }

    //OKOKOKOK YA ESTA
    public function comprobarCantProductoAlmacen($producto, $almacen, $cantidad, EntityManager $em)
    {
        $result = $this->obtenerCantidadProductosAlmancen($producto, $almacen, $em);
        //Devuelve si si o no existe la cantidad  o 'No Existe' en caso de que no exista
        if (is_numeric($result)) {
            return $result >= $cantidad;
        } else {
            //'No Existe'
            return $result;
        }
    }


    //OKOKOKOKOKO YA ESTA
    public function comprobarCantProductoSeriadoAlmacen($producto, $serial, $almacen, EntityManager $em)
    {
        $warehouse_id = $almacen;
        $product_id = $producto;
        $serial_nro = $serial;

        $qb = $em->createQueryBuilder();
        $query = $qb->select('sum(bs.qty) as existencia')
            ->from('BusetaBodegaBundle:BitacoraSerial', 'bs')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('bs.serial', ':serial'),
                    $qb->expr()->eq('bs.producto', ':producto'),
                     $qb->expr()->eq('bs.almacen', ':almacen')
                )
            )->setParameters(array(
                'serial' =>  $serial_nro ,
                'producto' => $product_id ,
                'almacen' => $warehouse_id
            ));

        return $query->getQuery()->getSingleScalarResult();

        //antigua vía

//antigua via

/*
        $product_id = $producto;
        $serial_nro = $serial;
          $rsm = new ResultSetMapping();
        $rsm->addScalarResult('sp_CantidadSerialesProductoEnAlmacen', 'res');
        $q = $em->createNativeQuery(
            "SELECT sp_CantidadSerialesProductoEnAlmacen (:warehouse_id, :product_id, :serial) AS sp_CantidadSerialesProductoEnAlmacen",
            $rsm
        );
        $q->setParameter('warehouse_id', $warehouse_id);
        $q->setParameter('product_id', $product_id);
        $q->setParameter('serial', $serial_nro);

        $res = $q->getSingleScalarResult();
        return $res;*/

        //Devuelve la cantidad existente o 'No Existe' en caso de que no exista

    }

    //OKOKOKOKOK YA ESTA
    public function comprobarCantProductoSeriadoEmpresa($producto, $serial, EntityManager $em)
    {

        $product_id = $producto;
        $serial_nro = $serial;

        $qb = $em->createQueryBuilder();
        $query = $qb->select('sum(bs.qty) as existencia')
            ->from('BusetaBodegaBundle:BitacoraSerial', 'bs')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('bs.serial', ':serial'),
                    $qb->expr()->eq('bs.producto', ':producto')
                )
            )->setParameters(array(
                'serial' =>  $serial_nro ,
                'producto' => $product_id
            ));

        return $query->getQuery()->getSingleScalarResult();

        //antigua vía


        $product_id = $producto;
        $serial_nro = $serial;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('sp_CantidadSerialesProductoEnEmpresa', 'res');
        $q = $em->createNativeQuery(
            "SELECT sp_CantidadSerialesProductoEnEmpresa (:warehouse_id, :serial) AS sp_CantidadSerialesProductoEnEmpresa",
            $rsm
        );
        $q->setParameter('serial', $serial_nro);
        $q->setParameter('product_id', $product_id);
        $res = $q->getSingleScalarResult();

        //Devuelve la cantidad existente o 'No Existe' en caso de que no exista
        return $res;
    }

    /**
     * TODO OK!
     *
     * Segun veo devuelve los seriales que estan en un almacen, para un producto, que tienen saldo(los consumidos no!!!)
     * aplica un distinct tambien para no devolverlos 2 o mas veces
     *
     * @param               $producto
     * @param               $almacen
     * @param EntityManager $em
     *
     * @return array|mixed
     */
    public function getListaSerialesTeoricoEnAlmacen($producto, $almacen, EntityManager $em)
    {
        $warehouse_id = $almacen ; // 31;
        $product_id = $producto; // 5337;

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('serial', 'serial');

        $q = $em->createNativeQuery(
            "CALL sp_GetSerialesProductoEnAlmacenPlus (:warehouse_id, :product_id)",
            $rsm
        );

        $q->setParameter('warehouse_id', $warehouse_id);
        $q->setParameter('product_id', $product_id);

        $result = $q->getResult() ;

        //Devuelve la cantidad existente o 'No Existe' en caso de que no exista
        if  ($result) {
            return $result;
        } else {
            return array();
        }

        //antigua via
        /*$connection = $em->getConnection();
        $statement = $connection->prepare("CALL sp_GetSerialesProductoEnAlmacenPlus (:warehouse_id, :product_id)");
        $statement->bindParam(':warehouse_id',$warehouse_id);
        $statement->bindParam(':product_id',$product_id);
        $statement->execute();
        $result = $statement->fetchall();
        $statement->closeCursor();*/

    }

    /**
     * OKOKOKOKO YA ESTA
     * Segun veo devuelve los seriales que estan en un almacen, para un producto, que tienen saldo(los consumidos no!!!)
     * aplica un distinct tambien para no devolverlos 2 o mas veces
     *
     * @param $producto
     * @param $almacen
     * @param $em
     *
     * @return array
     */
    public function getListaSerialesEntitiesEnAlmacen($producto, $almacen, EntityManager $em)
    {

        $warehouse_id = $almacen ; // 31;
        $product_id = $producto; // 5337;

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Buseta\BodegaBundle\Entity\BitacoraSerial', 'bs');

        $rsm->addFieldResult('bs', 'id', 'id');
        $rsm->addJoinedEntityResult('Buseta\BodegaBundle\Entity\Bodega' , 'almacen', 'bs', 'almacen');
        $rsm->addFieldResult('almacen', 'warehouse_id', 'id');
        $rsm->addJoinedEntityResult('Buseta\BodegaBundle\Entity\Producto' , 'producto', 'bs', 'producto');
        $rsm->addFieldResult('producto', 'product_id', 'id');

        $rsm->addFieldResult('bs', 'serial', 'serial');
        $rsm->addFieldResult('bs', 'created', 'created');
        $rsm->addFieldResult('bs', 'updated	', 'updated	');
        $rsm->addFieldResult('bs', 'deleted	', 'deleted	');

        $rsm->addFieldResult('bs', 'movement_date', 'fechaMovimiento' );
        $rsm->addFieldResult('bs', 'movement_type	', 'tipoMovimiento' );
        $rsm->addFieldResult('bs', 'movement_qty', 'cantidadMovida' );

        $q = $em->createNativeQuery(
            "CALL sp_GetSerialesProductoEnAlmacenObjectsPlus (:warehouse_id, :product_id)",
            $rsm
        );

        $q->setParameter('warehouse_id', $warehouse_id);
        $q->setParameter('product_id', $product_id);

        return $q->getResult() ;
    }

    //????QUIZAS NO HAGA FALTA, SOLO LLAMAR EL DE ARRIBA
    public function getListaSerialesEntitiesEnAlmacenFilter($em, $filter)
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $producto = $filter->getProducto();
        $almacen = $filter->getAlmacen();

        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraSerial')->findBy(
            array(
                'producto' => $producto,
                'almacen' => $almacen,
            )
        );

        $lista_seriales = array();

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraSerial $bitacora */
            $serial = $bitacora->getSerial();
            //si esta en existencia, porque puede star en 0
            if (($filter->getSerial() == "") or (strpos("s".$serial, $filter->getSerial()))) {
                $cantidadDisponible = $this->comprobarCantProductoSeriadoAlmacen($producto, $serial, $almacen, $em);
                if ($cantidadDisponible > 0) {
                    $lista_seriales[] = $bitacora;
                }
            }
        }

        $repeated_indices = array();
        //quitar los elementos repetidos del array de resultados
        foreach ($lista_seriales as $lista_serialo) {
            $idx = 0;
            foreach ($lista_seriales as $lista_seriali) {
                if ($lista_seriali != $lista_serialo) {
                    if ($lista_seriali->getSerial() == $lista_serialo->getSerial()) {
                        $repeated_indices[] = $idx;
                    }
                }
                $idx = $idx + 1;
            }
        }
        foreach ($repeated_indices as $index) {
            unset($lista_seriales[$index]);
        }

        return $lista_seriales;
    }

    //????
    public function generarInformeCostos($bitacoras, $em)
    {
        $almacenes = $em->getRepository('BusetaBodegaBundle:Bodega')->findAll();
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')->findAll();

        $almacenesArray = array(array());
        $pos = 0;
        $almacenesArray[$pos]['almacen'] = null;
        $almacenesArray[$pos]['producto'] = null;
        $almacenesArray[$pos]['cantidad'] = null;
        $almacenesArray[$pos]['costos'] = null;

        //Recorro cada almacen
        foreach ($almacenes as $almacen) {
            $cantidadPedido = 0;
            $costos = 0;

            //Busco cada producto existente para la bitacora actual
            foreach ($productos as $producto) {
                foreach ($bitacoras as $bitacora) {
                    /** @var \Buseta\BodegaBundle\Entity\BitacoraAlmacen $bitacora */
                    if ($producto == $bitacora->getProducto() && $bitacora->getAlmacen() == $almacen) {
                        //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
                        if (self::movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido += $bitacora->getCantidadMovida();
                        }
                        if (self::movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido -= $bitacora->getCantidadMovida();
                        }

                        foreach ($producto->getCostoProducto() as $costos) {
                            if ($costos->getActivo()) {
                                $costoProducto = ($costos->getCosto());
                            }
                        }
                        $costos = $cantidadPedido * $costoProducto;
                    }
                }

                //Actualizo el informeStock
                if ($cantidadPedido != 0) {
                    $almacenesArray[$pos]['almacen'] = $almacen;
                    $almacenesArray[$pos]['producto'] = $producto;
                    $almacenesArray[$pos]['cantidad'] = $cantidadPedido;
                    $almacenesArray[$pos]['costos'] = $costos;
                    $pos += 1;
                }

                //Reinicio cantidad de pedidos
                $costos = 0;
            }
        }

        return $almacenesArray;
    }


    //OKOKOKOKOKOKOK YA ESTA
    public function obtenerCantidadProductosAlmancen($producto, $almacen, EntityManager $em)
    {

         try {
        $qb = $em->createQueryBuilder();
        $query = $qb->select('sum(b.qty) as existencia')
            ->from('BusetaBodegaBundle:BitacoraAlmacen', 'b')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('b.almacen', ':almacen'),
                    $qb->expr()->eq('b.producto', ':producto')
                )
            )->setParameters(array(
                'almacen' =>  $almacen ,
                'producto' => $producto
            ));

        return $query->getQuery()->getSingleScalarResult();

        } catch ( NoResultException $e) {
            return 0;
        } catch ( \Exception $e) {
             // hay que ver
             return 0;
         }


        //Antigua vía
        /*$rsm = new ResultSetMapping();
        $rsm->addScalarResult('sp_CantidadProductoEnAlmacen', 'res');
        $q = $em->createNativeQuery(
            "SELECT sp_CantidadProductoEnAlmacen (:warehouse_id, :product_id) AS sp_CantidadProductoEnAlmacen",
            $rsm
        );
        $q->setParameter('warehouse_id', $warehouse_id);
        $q->setParameter('product_id', $product_id);
        $res = $q->getSingleScalarResult();*/

        //Devuelve la cantidad existente o 'No Existe' en caso de que no exista
       // return $res;
    }


    /* Devuelve un booleano al comprobar si un Autobus se encuentra en la ListaNegraCombustible */
    public function comprobarAutobusesListaNegra($autobus, $em)
    {
        $listaNegrasCombustible = $em->getRepository('BusetaBusesBundle:ListaNegraCombustible')->findAll();
        $today = new \DateTime('now');

        foreach ($listaNegrasCombustible as $lista) {

            //Comprobar si el autobus se encuentra en la lista y la fecha actual
            if ($lista->getAutobus() == $autobus) {
                if ($lista->getFechaInicio() <= $today && $lista->getFechaFinal() >= $today) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if movementType is positive.
     *
     * @param $tipoMovimiento
     *
     * @return bool
     */
    static public function movementTypeComparePlus($tipoMovimiento)
    {
        return $tipoMovimiento === BusetaBodegaMovementTypes::VENDOR_RECEIPTS
        || $tipoMovimiento === BusetaBodegaMovementTypes::MOVEMENT_TO
        || $tipoMovimiento === BusetaBodegaMovementTypes::PRODUCTION_PLUS
        || $tipoMovimiento === BusetaBodegaMovementTypes::INVENTORY_IN
        || $tipoMovimiento === BusetaBodegaMovementTypes::INTERNAL_CONSUMPTION_PLUS;
    }

    /**
     * Check if movementType is negative.
     *
     * @param $tipoMovimiento
     *
     * @return bool
     */
    static public function movementTypeCompareMinus($tipoMovimiento)
    {
        return $tipoMovimiento === BusetaBodegaMovementTypes::VENDOR_RETURNS
        || $tipoMovimiento === BusetaBodegaMovementTypes::MOVEMENT_FROM
        || $tipoMovimiento === BusetaBodegaMovementTypes::PRODUCTION_MINUS
        || $tipoMovimiento === BusetaBodegaMovementTypes::INVENTORY_OUT
        || $tipoMovimiento === BusetaBodegaMovementTypes::INTERNAL_CONSUMPTION_MINUS;
    }
}

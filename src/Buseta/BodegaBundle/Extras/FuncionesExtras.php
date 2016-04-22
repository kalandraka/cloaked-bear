<?php

namespace Buseta\BodegaBundle\Extras;

use Buseta\BodegaBundle\BusetaBodegaMovementTypes;

class FuncionesExtras
{
    public function ImporteLinea($cantidad_pedido, $precio_unitario, $impuesto = null, $porciento_descuento = 0)
    {
        $importeBruto       = $cantidad_pedido * $precio_unitario;
        $importeDescuento   = $importeBruto * $porciento_descuento / 100;
        $importeImpuesto    = 0;

        if ($impuesto) {
            $tipoImpuesto = $impuesto->getTipo();
            $tarifaImpuesto = $impuesto->getTarifa();

            if ($tipoImpuesto == "fijo") {
                $importeImpuesto = $tarifaImpuesto;
            } elseif ($tipoImpuesto == "porcentaje") {
                $importeImpuesto    = $importeBruto * $tarifaImpuesto / 100;
            }
        }

        return $importeBruto + $importeImpuesto - $importeDescuento;;
    }

    public function ActualizarInformeStock($busqueda, $em)
    {
        $almacenes = $em->getRepository('BusetaBodegaBundle:Bodega')->findAll();
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')->findAll();

        $datos = $busqueda->getData();

        //Recorro cada almacen
        foreach ($almacenes as $almacen) {

            //Obtengo la bitacora para el almacen actual
            $bitacoras = $em->getRepository('BusetaBodegaBundle:InformeStock')->buscarAlmacenBitacora($almacen,
                $datos['fecha']);

            $cantidadPedido = 0;

            //Busco cada producto existente para la bitacora actual
            foreach ($productos as $producto) {
                foreach ($bitacoras as $bitacora) {
                    /** @var \Buseta\BodegaBundle\Entity\BitacoraAlmacen $bitacora */
                    if ($producto == $bitacora->getProducto()) {
                        //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
                        if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido += $bitacora->getCantidadMovida();
                        }
                        if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
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

    public function generarInformeStock($bitacoras, $em)
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
                        if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido += $bitacora->getCantidadMovida();
                        }
                        if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
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

    public function comprobarCantProductoAlmacen($producto, $almacen, $cantidad, $em)
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $cantidadPedido = 0;
        $existe = false;
        // $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraAlmacen')->findAll();

        //hallar solo las lineas de bitacora del producto y almacen especificado
        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraAlmacen')->findBy(array(
            'producto' => $producto,
            'almacen' => $almacen,
        ));

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraAlmacen $bitacora */
            //Si se encuentra en la bitácora el almacen y producto seleccionado
            /*            if ($bitacora->getAlmacen() == $almacen && $bitacora->getProducto() == $producto) {
                            $existe = true;*/
            //Comprobar tipo de movimiento para realizar operación de sustracción o adición
            //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
            $existe = true;
            if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                $cantidadPedido += $bitacora->getCantidadMovida();
            }
            if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                $cantidadPedido -= $bitacora->getCantidadMovida();
            }
            //}
        }

        if ($existe) {
            return $cantidadPedido - $cantidad;
        }

        //
        return 'No existe';
    }

    public function comprobarCantProductoSeriadoAlmacen($producto, $serial, $almacen, $em)
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $cantidad = 0;
        $existe = false;

        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraSerial')->findBy(array(
            'producto' => $producto,
            'serial' => $serial,
            'almacen' => $almacen,
        ));

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraSerial $bitacora */
            //Si se encuentra en la bitácora el almacen y producto seleccionado
            $existe = true;
            //Comprobar tipo de movimiento para realizar operación de sustracción o adición
            //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)

            if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                $cantidad += $bitacora->getCantidadMovida();
            }
            if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                $cantidad -= $bitacora->getCantidadMovida();
            }
        }

        if ($existe) {
            return $cantidad;
        }

        return 'No existe';
    }

    public function comprobarCantProductoSeriadoEmpresa( $producto, $serial, $em )
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $cantidad = 0;
        $existe = false;

        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraSerial')->findBy(array(
            'producto' => $producto,
            'serial' => $serial,
        ));

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraSerial $bitacora */
            //Si se encuentra en la bitácora el almacen y producto seleccionado
            $existe = true;
            //Comprobar tipo de movimiento para realizar operación de sustracción o adición
            //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
            if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                $cantidad += $bitacora->getCantidadMovida();
            }
            if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                $cantidad -= $bitacora->getCantidadMovida();
            }
        }

        if ($existe) {
            return $cantidad;
        }

        return 'No existe';
    }

    public function getListaSerialesTeoricoEnAlmacen( $producto, $almacen, $em )
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraSerial')->findBy(array(
            'producto' => $producto,
            'almacen' => $almacen,
        ));

        $lista_seriales = array();

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraSerial $bitacora */
             $serial = $bitacora->getSerial();
             //si esta en existencia, porque puede star en 0
             $cantidadDisponible =  $this->comprobarCantProductoSeriadoAlmacen( $producto, $serial, $almacen, $em );
             if ($cantidadDisponible>0) {
                 $lista_seriales[] = $serial;//agregar al array
             }
        }

        //quitar los elementos repetidos del array de resultados
        $lista_seriales = array_unique($lista_seriales);

        return $lista_seriales;
    }

    public function getListaSerialesEntitiesEnAlmacen( $producto, $almacen, $em )
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraSerial')->findBy(array(
            'producto' => $producto,
            'almacen' => $almacen,
        ));

        $lista_seriales = array();

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraSerial $bitacora */
            $serial = $bitacora->getSerial();
            //si esta en existencia, porque puede star en 0
            $cantidadDisponible =  $this->comprobarCantProductoSeriadoAlmacen( $producto, $serial, $almacen, $em );
            if ($cantidadDisponible>0) {
                $lista_seriales[] = $bitacora;
            }
        }

        $repeated_indices = array();
        //quitar los elementos repetidos del array de resultados
        foreach ($lista_seriales as $lista_serialo) {
            $idx = 0;
            foreach ($lista_seriales as $lista_seriali) {
                if($lista_seriali != $lista_serialo)
                {
                    if($lista_seriali->getSerial() == $lista_serialo->getSerial())
                    {
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

    public function getListaSerialesEntitiesEnAlmacenFilter( $em, $filter )
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $producto = $filter->getProducto();
        $almacen = $filter->getAlmacen();

        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraSerial')->findBy(array(
            'producto' => $producto,
            'almacen' => $almacen,
        ));

        $lista_seriales = array();

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraSerial $bitacora */
            $serial = $bitacora->getSerial();
            //si esta en existencia, porque puede star en 0
            if(($filter->getSerial() == "") or (strpos("s".$serial, $filter->getSerial()))) {
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
                if($lista_seriali != $lista_serialo)
                {
                    if($lista_seriali->getSerial() == $lista_serialo->getSerial())
                    {
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
                        if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                            $cantidadPedido += $bitacora->getCantidadMovida();
                        }
                        if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())){
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

    public function obtenerCantidadProductosAlmancen($producto, $almacen, $em)
    {
        /**@var \Doctrine\Common\Persistence\ObjectManager $em */

        $cantidadReal = 0;
        //$existe = false;
        //Obtengo las bitacoras
        //$bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraAlmacen')->findAll();
        //hallar solo las lineas de bitacora del producto y almacen especificado
        $bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraAlmacen')->findBy(array(
            'producto' => $producto,
            'almacen' => $almacen,
        ));

        foreach ($bitacoras as $bitacora) {
            /** @var \Buseta\BodegaBundle\Entity\BitacoraAlmacen $bitacora */
            //Si se encuentra en la bitácora el almacen y producto seleccionado
            //if ($bitacora->getAlmacen() == $almacen && $bitacora->getProducto() == $producto) {
                //$existe = true;
                //Comprobar tipo de movimiento para realizar operación de sustracción o adición
                //Identifico el tipoMovimiento (NO SE HA IMPLEMENTADO COMPLETAMENTE AÚN)
                if ($this->movementTypeComparePlus($bitacora->getTipoMovimiento())) {
                    $cantidadReal += $bitacora->getCantidadMovida();
                }
                if ($this->movementTypeCompareMinus($bitacora->getTipoMovimiento())) {
                    $cantidadReal -= $bitacora->getCantidadMovida();
                }
            //}
        }

        return $cantidadReal;
    }

    /* Devuelve un booleano al comprobar si un Autobus se encuentra en la ListaNegraCombustible */
    public function comprobarAutobusesListaNegra($autobus, $em)
    {
        $listaNegrasCombustible = $em->getRepository('BusetaBusesBundle:ListaNegraCombustible')->findAll();
        $today = new \DateTime('now');

        foreach ($listaNegrasCombustible as $lista) {

            //Comprobar si el autobus se encuentra en la lista y la fecha actual
            if ($lista->getAutobus() == $autobus) {
                if($lista->getFechaInicio() <= $today && $lista->getFechaFinal() >= $today) {
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
    private function movementTypeComparePlus($tipoMovimiento)
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
    private function movementTypeCompareMinus($tipoMovimiento)
    {
        return $tipoMovimiento === BusetaBodegaMovementTypes::VENDOR_RETURNS
        || $tipoMovimiento === BusetaBodegaMovementTypes::MOVEMENT_FROM
        || $tipoMovimiento === BusetaBodegaMovementTypes::PRODUCTION_MINUS
        || $tipoMovimiento === BusetaBodegaMovementTypes::INVENTORY_OUT
        || $tipoMovimiento === BusetaBodegaMovementTypes::INTERNAL_CONSUMPTION_MINUS;
    }
}

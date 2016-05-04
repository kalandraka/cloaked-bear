<?php

namespace Buseta\BodegaBundle\Manager;

use Buseta\BodegaBundle\Entity\BitacoraSerial;
use Buseta\BodegaBundle\Model\BitacoraSerialEventModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Monolog\Logger;
use Buseta\BodegaBundle\Extras\GeneradorSeriales;
use Buseta\BodegaBundle\Exceptions\NotValidBitacoraTypeException;
use Symfony\Component\Security\Core\Util\ClassUtils;
use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BitacoraSerialManager
 *
 * @package Buseta\BodegaBundle\Manager
 */
class BitacoraSerialManager
{
    const BULK_SIZE = 400;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var \Buseta\BodegaBundle\Extras\GeneradorSeriales
     */
    private $generadorSeriales;


    /**
     * @param EntityManager $em
     * @param Logger $logger
     * @param ValidatorInterface $validator
     * @param GeneradorSeriales $generadorSeriales
     */
    function __construct(EntityManager $em, Logger $logger, ValidatorInterface $validator, GeneradorSeriales $generadorSeriales)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->generadorSeriales = $generadorSeriales;
    }


    /**
     * @param $albaranLinea  \Buseta\BodegaBundle\Entity\AlbaranLinea
     * @param $movementType string
     *
     * @return bool
     */
    public function guardarSerialesDesdeAlbaranLinea($albaranLinea, $movementType)
    {
        try {

            $strSeriales = $albaranLinea->getSeriales();
            $seriales = $this->generadorSeriales->getListaDeSeriales($strSeriales);
            $fe = new FuncionesExtras();

            //Si llega aqui todo bien y no hay errores de validacion
            foreach ($seriales as $serial) {

                    /*$cantidadDisponible = $fe->comprobarCantProductoSeriadoAlmacen(
                    $albaranLinea->getProducto(),
                    $serial,
                    $albaranLinea->getAlbaran()->getAlmacen(),
                    $this->em);*/

                $cantidadDisponible = $fe->comprobarCantProductoSeriadoEmpresa(
                    $albaranLinea->getProducto(),
                    $serial,
                    $this->em);

                //Comprobar la existencia del producto en la empresa
                if ($cantidadDisponible > 0) {
                    return $error = sprintf('El serial (%s) del producto (%s) ya se encuentra en existencia en la empresa.',
                        $serial, $albaranLinea->getProducto()->getNombre());
                }

                //creo la bitacora de seriales
                $bitacoraSerial = new BitacoraSerial();
                $bitacoraSerial
                    ->setProducto($albaranLinea->getProducto())
                    ->setCantidadMovida(1)
                    ->setSerial($serial)
                    ->setFechaMovimiento($albaranLinea->getAlbaran()->getFechaMovimiento())
                    ->setEntradaSalidaLinea($albaranLinea)
                    ->setAlmacen($albaranLinea->getAlbaran()->getAlmacen())
                    ->setTipoMovimiento($movementType);

                //validacion y creacion de una linea  de bitacoraSerial
                $result = $this->legacyCreateRegistry($bitacoraSerial);
                if ($result === false) {
                    return $error = 'Ocurrio un error salvando en la bitacora de seriales';
                };
            }

            //llega aqui si todo ok, aqui todavia no se hace flush
            return true;

        } catch (\Exception $e) {
            $this->logger->error(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));
            //hacer rollback en el futuro
            return $error = 'Error guardando el albaran linea';
        }

    }


    /**
     * @param $inventarioFisicoLinea  \Buseta\BodegaBundle\Entity\InventarioFisicoLinea
     * @param $movementType string
     * @return bool
     */
    public function guardarSerialesDesdeInventarioFisicoLinea($inventarioFisicoLinea, $movementType)
    {
        try {

            $strSeriales = $inventarioFisicoLinea->getSeriales();

            $serialesReal = $this->generadorSeriales->getListaDeSeriales($strSeriales);

            $fe = new FuncionesExtras();

            $serialesTeoricos = $fe->getListaSerialesTeoricoEnAlmacen(
                $inventarioFisicoLinea->getProducto(),
                $inventarioFisicoLinea->getInventarioFisico()->getAlmacen(),
                $this->em );

             //los seriales que estan en los teoricos y no estan en los reales
             $seriales_IMenos  = array_diff($serialesTeoricos,$serialesReal) ;
             //los seriales que estan en los reales y no estan en los teoricos
             $seriales_IMas    = array_diff($serialesReal,$serialesTeoricos) ;

            //ciclo para quitar seriales con I- de la bitacora de seriales
            foreach ($seriales_IMenos as $serial) {
                //creo la bitacora de seriales
                $bitacoraSerial = new BitacoraSerial();
                $bitacoraSerial
                    ->setProducto($inventarioFisicoLinea->getProducto())
                    ->setCantidadMovida(1)
                    ->setSerial($serial)
                    ->setFechaMovimiento($inventarioFisicoLinea->getInventarioFisico()->getFecha())
                    ->setInventarioLinea($inventarioFisicoLinea)
                    ->setAlmacen($inventarioFisicoLinea->getInventarioFisico()->getAlmacen())
                    ->setTipoMovimiento('I-');

                //validacion y creacion de una linea  de bitacoraSerial
                $result = $this->legacyCreateRegistry($bitacoraSerial);
                if ($result === false) {
                    return $error = 'Ocurrio un error salvando en la bitacora de seriales';
                };
            }

            //ciclo para incorporar seriales con I+ de la bitacora de seriales
            foreach ($seriales_IMas as $serial) {

                //verifico que los seriales que estoy adicionando no se encuentren en la empresa
                //parecido alalbaran de entrada
                $cantidadDisponible = $fe->comprobarCantProductoSeriadoEmpresa(
                    $inventarioFisicoLinea->getProducto(),
                    $serial,
                    $this->em);
                //Comprobar la existencia del producto en la empresa
                if ($cantidadDisponible > 0) {
                    return $error = sprintf('El serial (%s) del producto (%s) ya se encuentra en existencia en la empresa.',
                        $serial, $inventarioFisicoLinea->getProducto()->getNombre());
                }

                //creo la bitacora de seriales
                $bitacoraSerial = new BitacoraSerial();
                $bitacoraSerial
                    ->setProducto($inventarioFisicoLinea->getProducto())
                    ->setCantidadMovida(1)
                    ->setSerial($serial)
                    ->setFechaMovimiento($inventarioFisicoLinea->getInventarioFisico()->getFecha())
                    ->setInventarioLinea($inventarioFisicoLinea)
                    ->setAlmacen($inventarioFisicoLinea->getInventarioFisico()->getAlmacen())
                    ->setTipoMovimiento('I+');

                //validacion y creacion de una linea  de bitacoraSerial
                $result = $this->legacyCreateRegistry($bitacoraSerial);
                if ($result === false) {
                    return $error = 'Ocurrio un error salvando en la bitacora de seriales';
                };
            }

            //llega aqui si todo ok, aqui todavia no se hace flush
            return true;

        } catch (\Exception $e) {
            $this->logger->error(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));
            //hacer rollback en el futuro
            return $error = 'Error guardando el albaran linea';
        }

    }

    /**
     * @param $movimientoProducto  \Buseta\BodegaBundle\Entity\MovimientosProductos
     * @param $movementType string
     * @return bool
     */
    public function guardarSerialesDesdeMovimientoProducto($movimientoProducto, $movementType)
    {

        try {

            $strSeriales = $movimientoProducto->getSeriales();
            $seriales = $this->generadorSeriales->getListaDeSeriales($strSeriales);
            $fe = new FuncionesExtras();

            foreach ($seriales as $serial) {
                //para cada serial compruebo que existe un producto seriado en la base de datos
                //y luego si existe en el almacen de labitacora origen y hay la cantidad suficiente

                $cantidadDisponible = $fe->comprobarCantProductoSeriadoAlmacen(
                    $movimientoProducto->getProducto(),
                    $serial,
                    $movimientoProducto->getMovimiento()->getAlmacenOrigen(),
                    $this->em);

                //solo compruebo para M-
                if ($movementType === 'M-') {
                    //Comprobar la existencia del producto en la bodega seleccionada
                    if ($cantidadDisponible === 'No existe') {
                        return $error = sprintf('No existe el serial (%s) del producto (%s) en el almacen de origen',
                            $serial, $movimientoProducto->getProducto()->getNombre());
                    } elseif ($cantidadDisponible <= 0) {
                        return $error = sprintf('No existe la cantidad del serial (%s) del producto (%s) en el almacen de origen',
                            $serial, $movimientoProducto->getProducto()->getNombre());
                    }
                }

                //creacion de la linea de bitacora de seriales
                $bitacoraSerial = new BitacoraSerial();
                $bitacoraSerial
                    ->setProducto($movimientoProducto->getProducto())
                    ->setCantidadMovida(1)
                    ->setSerial($serial)
                    ->setFechaMovimiento($movimientoProducto->getMovimiento()->getFechaMovimiento())
                    ->setMovimientoLinea($movimientoProducto)
                    ->setTipoMovimiento($movementType);

                if ($movementType === 'M-') {
                    $bitacoraSerial->setAlmacen($movimientoProducto->getMovimiento()->getAlmacenOrigen());
                } elseif ($movementType == 'M+') {
                    $bitacoraSerial->setAlmacen($movimientoProducto->getMovimiento()->getAlmacenDestino());
                } else {
                    throw new NotValidBitacoraTypeException('Tipo de Movimiento no valido' . $movementType);
                }

                //validacion y creacion de una linea  de bitacoraSerial
                $result = $this->legacyCreateRegistry($bitacoraSerial);
                if ($result === false) {
                    return $error = 'Ocurrio un error salvando en la bitacora de seriales';
                };

            }

            //llega aqui si todo ok, aqui todavia no se hace flush
            return true;

        } catch (\Exception $e) {
            $this->logger->error(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));
            return $error = 'Ocurrio un error salvando en la bitacora de seriales';
        }

    }

    /**
     * @param $salidaBodegaProducto  \Buseta\BodegaBundle\Entity\SalidaBodegaProducto
     * @param $movementType string
     * @return bool
     */
    public function guardarSerialesDesdeSalidaBodegaProducto($salidaBodegaProducto, $movementType)
    {
        try {

            $strSeriales = $salidaBodegaProducto->getSeriales();
            $seriales = $this->generadorSeriales->getListaDeSeriales($strSeriales);
            $fe = new FuncionesExtras();

            foreach ($seriales as $serial) {
                //para cada serial compruebo que existe un producto seriado en la base de datos
                //y luego si existe en el almacen de labitacora origen y hay la cantidad suficiente
                $cantidadDisponible = $fe->comprobarCantProductoSeriadoAlmacen(
                    $salidaBodegaProducto->getProducto(),
                    $serial,
                    $salidaBodegaProducto->getSalida()->getAlmacenOrigen(),
                    $this->em);

                //solo compruebo para M-
                if ($movementType === 'M-') {
                    //Comprobar la existencia del producto en la bodega seleccionada
                    if ($cantidadDisponible === 'No existe') {
                        return $error = sprintf('No existe el serial (%s) del producto (%s) en el almacen de origen',
                            $serial, $salidaBodegaProducto->getProducto()->getNombre());
                    } elseif ($cantidadDisponible <= 0) {
                        return $error = sprintf('No existe la cantidad del serial (%s) del producto (%s) en el almacen de origen',
                            $serial, $salidaBodegaProducto->getProducto()->getNombre());
                    }
                }

                //creacion de la linea de bitacora de seriales
                $bitacoraSerial = new BitacoraSerial();
                $bitacoraSerial
                    ->setProducto($salidaBodegaProducto->getProducto())
                    ->setCantidadMovida(1)
                    ->setSerial($serial)
                    ->setFechaMovimiento($salidaBodegaProducto->getSalida()->getFecha())
                    ->setTipoMovimiento($movementType)
                    ->setProduccionLinea(sprintf('%s,%d', ClassUtils::getRealClass($salidaBodegaProducto),
                        $salidaBodegaProducto->getId()));

                if ($movementType === 'M-') {
                    $bitacoraSerial->setAlmacen($salidaBodegaProducto->getSalida()->getAlmacenOrigen());
                } elseif ($movementType === 'M+') {
                    $bitacoraSerial->setAlmacen($salidaBodegaProducto->getSalida()->getAlmacenDestino());
                } else {
                    throw new NotValidBitacoraTypeException('Tipo de Salida de Bodega no valido' . $movementType);
                }

                //validacion y creacion de una linea  de bitacoraSerial
                $result = $this->legacyCreateRegistry($bitacoraSerial);
                if ($result === false) {
                    return $error = 'Ocurrio un error salvando en la bitacora de seriales';
                };

            }

            //llega aqui si todo ok, aqui todavia no se hace flush
            return true;

        } catch (\Exception $e) {
            $this->logger->error(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));

            return $error = 'Ocurrio un error salvando en la bitacora de seriales';
        }

    }

    /**
     * @param BitacoraSerial $bitacora
     * @return bool
     *
     * @deprecated
     */
    public function legacyCreateRegistry(BitacoraSerial $bitacora)
    {
        try {
            //el validator valida por los assert de la entity
            $validationOrigen = $this->validator->validate($bitacora);
            if ($validationOrigen->count() === 0) {
                $this->em->persist($bitacora);
            } else {
                $errors = '';
                foreach ($validationOrigen->getIterator() as $param => $error) {
                    $errors .= sprintf('%s: %s. ', $param, $error);
                }
                $this->logger->error(sprintf('BitacoraSeriales.Validation: %s', $errors));
                return 'Error en la validacion de la bitacora de Seriales';
            }

            //no hubo error
            return true;

        } catch (\Exception $e) {
            $this->logger->error(sprintf('BitacoraSeriales.Persist: %s', $e->getMessage()));
            //hacer rollback en el futuro
            return 'Error guardando la bitacora de seriales';
        }

    }

    /**
     * Creates Bitacora Serial registry
     *
     * @param BitacoraSerialEventModel $bitacoraSerialEventModel
     * @param bool                     $flush
     *
     * @return bool
     */
    public function createRegistry(BitacoraSerialEventModel $bitacoraSerialEventModel, $flush=false)
    {
        try {
            $registry = new BitacoraSerial();
            $registry->setAlmacen($bitacoraSerialEventModel->getWarehouse());
            $registry->setProducto($bitacoraSerialEventModel->getProduct());
            $registry->setSerial($bitacoraSerialEventModel->getSerial());
            $registry->setCantidadMovida($bitacoraSerialEventModel->getMovementQty());
            $registry->setFechaMovimiento($bitacoraSerialEventModel->getMovementDate());
            $registry->setTipoMovimiento($bitacoraSerialEventModel->getMovementType());

            if ($bitacoraSerialEventModel->getCallback() !== null) {
                call_user_func($bitacoraSerialEventModel->getCallback(), $registry);
            }

            $this->em->persist($registry);
            if ($flush) {
                $this->em->flush();
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));
            $bitacoraSerialEventModel->setError($e->getMessage());

            return false;
        }
    }

    public function nativeCreateRegistry(BitacoraSerialEventModel $bitacoraSerialEventModel, $flush=false)
    {
        try {
            $sql = sprintf('INSERT INTO d_bitacora_serial
(warehouse_id, product_id, movement_type, movement_date, movement_qty, created, serial)
VALUES (%d, %d, \'%s\', \'%s\', %d, \'%s\', \'%s\')',
                $bitacoraSerialEventModel->getWarehouse()->getId(),
                $bitacoraSerialEventModel->getProduct()->getId(),
                $bitacoraSerialEventModel->getMovementType(),
                date_format($bitacoraSerialEventModel->getMovementDate(), 'Y-m-d'),
                $bitacoraSerialEventModel->getMovementQty(),
                date_format(new \DateTime(), 'Y-m-d'),
                $bitacoraSerialEventModel->getSerial()
                );

            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->execute();

            return true;
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));
            $bitacoraSerialEventModel->setError($e->getMessage());

            return false;
        }
    }

    public function bulkNativeCreateRegistry(ArrayCollection $bitacoraSerialEventCollection, $flush=false)
    {
        try {
            $sql = $this->getBitacoraSerialInsertQuery();
            $count = 0;
            foreach ($bitacoraSerialEventCollection->getIterator() as $item) {
                $count++;
                $values = $this->getBitacoraSerialValues($item);
                $sql = $count === 1 ? sprintf('%s %s', $sql, $values) : sprintf('%s, %s', $sql, $values);

                if ($count % self::BULK_SIZE === 0) {
                    $stmt = $this->em->getConnection()->prepare($sql);
                    $stmt->execute();

                    $count = 0;
                    $sql = $this->getBitacoraSerialInsertQuery();
                }
            }

            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->execute();

            return true;
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('BitacoraSerial.Persist: %s', $e->getMessage()));

            return false;
        }
    }

    /**
     * Devuelve los valores en la estructura:
     * (warehouse_id, product_id, serial, movement_qty, movement_date, movement_type, inoutline_id, inventoryline_id,
     *  movementline_id, production_line, internal_consumption_line, created)
     *
     * @param BitacoraSerialEventModel $bitacoraSerialEventModel
     *
     * @return string
     */
    private function getBitacoraSerialValues(BitacoraSerialEventModel $bitacoraSerialEventModel)
    {
        $registry = new BitacoraSerial();
        $registry->setAlmacen($bitacoraSerialEventModel->getWarehouse());
        $registry->setProducto($bitacoraSerialEventModel->getProduct());
        $registry->setSerial($bitacoraSerialEventModel->getSerial());
        $registry->setCantidadMovida($bitacoraSerialEventModel->getMovementQty());
        $registry->setFechaMovimiento($bitacoraSerialEventModel->getMovementDate());
        $registry->setTipoMovimiento($bitacoraSerialEventModel->getMovementType());

        if ($bitacoraSerialEventModel->getCallback() !== null) {
            call_user_func($bitacoraSerialEventModel->getCallback(), $registry);
        }

        return sprintf('(%d, %d, \'%s\', %d, \'%s\', \'%s\', %s, %s, %s, %s, %s, \'%s\')',
            $registry->getAlmacen()->getId(),
            $registry->getProducto()->getId(),
            $registry->getSerial(),
            $registry->getCantidadMovida(),
            date_format($registry->getFechaMovimiento(), 'Y-m-d'),
            $registry->getTipoMovimiento(),
            $registry->getEntradaSalidaLinea() !== null ? $registry->getEntradaSalidaLinea()->getId() : 'null',
            $registry->getInventarioLinea() !== null ? $registry->getInventarioLinea()->getId() : 'null',
            $registry->getMovimientoLinea() !== null ? $registry->getMovimientoLinea()->getId() : 'null',
            $registry->getProduccionLinea() !== null ? sprintf('\'%s\'', $registry->getProduccionLinea()) : 'null',
            $registry->getConsumoInterno() !== null ? sprintf('\'%s\'', $registry->getConsumoInterno()) : 'null',
            date_format(new \DateTime(), 'Y-m-d H:i:s')
        );
    }

    private function getBitacoraSerialInsertQuery()
    {
        return 'INSERT INTO d_bitacora_serial
(warehouse_id, product_id, serial, movement_qty, movement_date, movement_type, inoutline_id, inventoryline_id, movementline_id, production_line, internal_consumption_line, created)
VALUES ';
    }
}

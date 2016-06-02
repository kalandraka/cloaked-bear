<?php

namespace Buseta\CombustibleBundle\Manager;


use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Buseta\CombustibleBundle\BusetaCombustibleEvents;
use Buseta\CombustibleBundle\Entity\ServicioCombustible;
use Buseta\CombustibleBundle\Event\FilterServicioCombustibleEvent;
use Buseta\CombustibleBundle\Exception\ServicioCombustibleException;
use Buseta\CombustibleBundle\Form\Model\ServicioCombustibleModel;
use Doctrine\ORM\EntityManager;
use HatueySoft\DateTimeBundle\Managers\FechaSistemaManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ServicioCombustibleManager
 *
 * @package Buseta\CombustibleBundle\Manager
 */
class ServicioCombustibleManager
{
    const USE_TRANSACTION = true;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \HatueySoft\DateTimeBundle\Managers\FechaSistemaManager
     */
    private $fechaSistemaManager;


    /**
     * ServicioCombustibleManager constructor.
     *
     * @param EntityManager             $em
     * @param Logger                    $logger
     * @param EventDispatcherInterface  $dispatcher
     * @param FechaSistemaManager       $fechaSistemaManager
     */
    public function __construct(
        EntityManager $em,
        Logger $logger,
        EventDispatcherInterface $dispatcher,
        FechaSistemaManager $fechaSistemaManager
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->fechaSistemaManager = $fechaSistemaManager;
    }

    /**
     * @param ServicioCombustibleModel $model
     *
     * @return ServicioCombustible|bool
     *
     * @throws ServicioCombustibleException
     */
    public function create(ServicioCombustibleModel $model)
    {
        $error = false;
        $confMarchamo = $this->em->getRepository('BusetaCombustibleBundle:ConfiguracionMarchamo')
            ->getActiveConfiguration();
        if (!$confMarchamo) {
            throw ServicioCombustibleException::UndefinedMarchamoConfiguration();
        }

        //Comparar la existencia de cantidadLibros disponibles para el nomenclador seleccionado
        $producto = $model->getCombustible()->getProducto();
        $bodega = $model->getCombustible()->getBodega();
        $cantidadProducto = $model->getCantidadLibros();

        $fe = new FuncionesExtras();
        $cantidadDisponible = $fe->comprobarCantProductoAlmacen($producto, $bodega, $cantidadProducto, $this->em);

        $servicioCombustible = new ServicioCombustible();
        $servicioCombustible->setCantidadLibros($model->getCantidadLibros());
        $servicioCombustible->setMarchamo1($model->getMarchamo1());
        $servicioCombustible->setMarchamo2($model->getMarchamo2());
        $servicioCombustible->setFecha($model->getFecha());

        if ($model->getCombustible() !== null) {
            $servicioCombustible->setCombustible($model->getCombustible());
        }
        if ($model->getChofer() !== null) {
            $servicioCombustible->setChofer($model->getChofer()->getChofer());
        }
        if ($model->getVehiculo() !== null) {
            $servicioCombustible->setVehiculo($model->getVehiculo());
        }
        try {
            if (self::USE_TRANSACTION) {
                $this->em->beginTransaction();
            }

            if ($this->dispatcher->hasListeners(BusetaCombustibleEvents::SERVICIO_COMBUSTIBLE_PRE_CREATE)) {
                $preCreate = new FilterServicioCombustibleEvent(
                    $servicioCombustible,
                    $servicioCombustible->getCombustible(),
                    $confMarchamo
                );
                $this->dispatcher->dispatch(BusetaCombustibleEvents::SERVICIO_COMBUSTIBLE_PRE_CREATE, $preCreate);
                if ($preCreate->getError()) {
                    $error = $preCreate->getError();
                }
            }

            $this->em->persist($servicioCombustible);

            if ($this->dispatcher->hasListeners(BusetaCombustibleEvents::SERVICIO_COMBUSTIBLE_POST_CREATE)) {
                $postCreate = new FilterServicioCombustibleEvent(
                    $servicioCombustible,
                    $servicioCombustible->getCombustible(),
                    $confMarchamo
                );
                $this->dispatcher->dispatch(BusetaCombustibleEvents::SERVICIO_COMBUSTIBLE_POST_CREATE, $postCreate);
                if ($postCreate->getError()) {
                    $error = $postCreate->getError();
                }
            }

            if (!$error) {
                $this->em->flush();

                if (self::USE_TRANSACTION) {
                    $this->em->commit();
                }

                return $servicioCombustible;
            }

            $this->logger->warning(sprintf(
                'No se pudo crear Servicio Combustible debido a errores previos: %s',
                $error
            ));
        } catch (\Exception $e) {
            $this->logger->critical(sprintf(
                'Ha ocurrido un error al crear Servicio Combustible. Detalles: {message: %s, file: %s, line: %d}',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        }

        if (self::USE_TRANSACTION) {
            $this->em->rollback();
        }

        return false;
    }
}

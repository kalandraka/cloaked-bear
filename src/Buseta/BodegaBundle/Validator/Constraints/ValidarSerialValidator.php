<?php

namespace Buseta\BodegaBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Buseta\BodegaBundle\Extras\GeneradorSeriales;
use Symfony\Component\Security\Core\Util\ClassUtils;

class ValidarSerialValidator extends ConstraintValidator
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var \Buseta\BodegaBundle\Extras\GeneradorSeriales
     */
    private $generadorSeriales;


    /**
     * ValidarSerialValidator constructor.
     *
     * @param ObjectManager     $em
     * @param GeneradorSeriales $generadorSeriales
     */
    function __construct(ObjectManager $em, GeneradorSeriales $generadorSeriales)
    {
        $this->em = $em;
        $this->generadorSeriales = $generadorSeriales;
    }

    /**
     * @param \Buseta\BodegaBundle\Entity\InventarioFisicoLinea $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        $error = false;
        $seriales = $this->generadorSeriales->getListaDeSeriales($entity->getSeriales());

        if ($entity->getProducto() !== null && ($entity->getProducto()->getTieneNroSerie() == true)) {

            if (($entity->getSeriales() !== null) && (trim($entity->getSeriales()) !== '')) {
                //validacion de los seriales segun la entidad
                $error = $this->validarSerialesSegunClase($entity, $seriales);
            } else {
                $error = 'Se requieren numeros de serie obligatoriamente para este producto!';
            }

        } else {
            if (($entity->getSeriales() !== null) && (trim($entity->getSeriales()) !== '')) {
                $error = 'Este producto no puede tener numeros de serie!';
            }
        }

        if ($error) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $error)
                ->atPath('seriales')
                ->addViolation();
        }

    }

    /**
     * @param $entity
     * @param $seriales
     * @return bool|string
     */
    function validarSerialesSegunClase($entity, $seriales)
    {
        $error = false;
        $cantidad = 0;

        switch (ClassUtils::getRealClass($entity)) {

            case 'Buseta\BodegaBundle\Entity\AlbaranLinea': {
                /* @var  $entity \Buseta\BodegaBundle\Entity\AlbaranLinea */
                $cantidad = $entity->getCantidadMovida();
                break;
            }
            case 'Buseta\BodegaBundle\Entity\InventarioFisicoLinea': {
                /* @var  $entity \Buseta\BodegaBundle\Entity\InventarioFisicoLinea */
                $cantidad = $entity->getCantidadReal();
                break;
            }
            case 'Buseta\BodegaBundle\Entity\MovimientosProductos': {
                /* @var  $entity \Buseta\BodegaBundle\Entity\MovimientosProductos */
                $cantidad = $entity->getCantidad();
                break;
            }
            case 'Buseta\BodegaBundle\Entity\SalidaBodegaProducto': {
                /* @var  $entity \Buseta\BodegaBundle\Entity\SalidaBodegaProducto */
                $cantidad = $entity->getCantidad();
                break;
            }
            case 'Buseta\CombustibleBundle\Entity\ServicioCombustible': {
                /* @var  $entity \Buseta\CombustibleBundle\Entity\ServicioCombustible */
                break;
            }
            default:
                break;
        }

        if ($seriales) {
            //verificar si la cantidad movida coincide con el numero de seriales entrado
            if (count($seriales) != $cantidad) {
                $error = 'La cantidad real no coincide con el numero de seriales';
            }
            //Si llega aqui todoo bien y no hay errores y termino
        } else {
            //si llega aqui es que hubo un error de validacion del serial
            $error = $this->generadorSeriales->getLasterror();
        }

        return $error;
    }
}




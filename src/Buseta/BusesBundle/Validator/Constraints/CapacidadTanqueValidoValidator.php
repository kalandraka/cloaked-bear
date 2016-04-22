<?php

namespace Buseta\BusesBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Component\PropertyAccess\StringUtil;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ExecutionContextInterface;

class CapacidadTanqueValidoValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($data, Constraint $constraint)
    {
        if ($data->getVehiculo() === null) {
            return;
        }

        //capacidad de combustible del vehiculo
        $capacidadCombustible = $data->getVehiculo()->getCapacidadTanque();

        //Validando que la capacidadTanque del Vehiculo sea mayor o igual la cantidadLibros entrada
        if ($capacidadCombustible <= $data->getCantidadLibros()) {
            $this->context->buildViolation('cantidadLibros', $constraint->messageCantidadLibros, array());
        }

        return;
    }
}

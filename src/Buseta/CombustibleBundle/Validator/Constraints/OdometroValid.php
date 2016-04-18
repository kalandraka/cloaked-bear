<?php

namespace Buseta\CombustibleBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class OdometroValid
 *
 * @package Buseta\CombustibleBundle\Validator\Constraints
 * @Annotation
 */
class OdometroValid extends Constraint
{
    public $message = 'El odometro insertado NO es mayor al anterior.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'combustible_odometro_validator';
    }
}

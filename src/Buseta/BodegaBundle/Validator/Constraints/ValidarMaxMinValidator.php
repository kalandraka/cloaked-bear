<?php

namespace Buseta\BodegaBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidarMaxMinValidator extends ConstraintValidator
{
    /**
     * @var ObjectManager
     */
    private $em;


    /**
     * ValidarMaxMinValidator constructor.
     *
     * @param ObjectManager $em
     */
    function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed      $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        /*  @var  \Buseta\BodegaBundle\Entity\ProductoTope  $entity*/

        $min = $entity->getMin();
        $max = $entity->getMax();

        if ($min > $max) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', 'El valor maximo debe ser mayor que el minimo')
                ->atPath('max');
         }
    }
}




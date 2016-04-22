<?php

namespace Buseta\CombustibleBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class Marchamo1ValidoValidator
 *
 * @package Buseta\CombustibleBundle\Validator\Constraints
 */
class Marchamo1ValidoValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Marchamo1ValidoValidator constructor.
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $data      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($data, Constraint $constraint)
    {
        $vehiculo = $data->getVehiculo();
        $marchamo1 = $data->getMarchamo1();
        if ($marchamo1 === null || $vehiculo === null) {
            return;
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('servicio.marchamo2')
            ->from('BusetaCombustibleBundle:ServicioCombustible', 'servicio')
            ->innerJoin('servicio.vehiculo', 'vehiculo')
            ->where($qb->expr()->in('servicio.id', 'SELECT MAX(s.id)
FROM BusetaCombustibleBundle:ServicioCombustible s
INNER JOIN s.vehiculo v
WHERE v.id = :vehiculo'))
            ->setParameters(array(
                'vehiculo' => $vehiculo->getId(),
            ));

        try {
            $query = $qb->getQuery();
            $result = $query->getSingleResult();
        } catch (\Exception $e) {
            $result = null;
        }

        if ($result !== null && !StringUtils::equals($result['marchamo2'], $marchamo1)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('marchamo1')
                ->addViolation();
        }

        return;
    }
}

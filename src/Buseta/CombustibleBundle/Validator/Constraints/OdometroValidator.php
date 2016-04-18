<?php

namespace Buseta\CombustibleBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class OdometroValidator
 *
 * @package Buseta\CombustibleBundle\Validator\Constraints
 */
class OdometroValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * OdometroValidator constructor.
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
     * @param mixed $data The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($data, Constraint $constraint)
    {
        $vehiculo = $data->getVehiculo();
        $odometro = $data->getOdometro();

        $qb = $this->em->createQueryBuilder();
        $qb->select('servicio.odometro')
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

        if ($result !== null && $result['odometro'] >= $odometro) {
        $this->context->buildViolation($constraint->message)
            ->atPath('odemetro')
            ->addViolation();
    }

        return;
    }
}

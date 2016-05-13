<?php

namespace Buseta\BodegaBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsCodigoValidator extends ConstraintValidator
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;


    /**
     * ContainsCodigoValidator constructor.
     *
     * @param ObjectManager $em
     */
    function __construct(ObjectManager $em)
    {
        $this->em       = $em;
    }

    /**
     * @param mixed      $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {

        $em = $this->em;
        $qry='
            SELECT   COUNT(DISTINCT p.codigo)
            FROM     BusetaBodegaBundle:Producto p
            WHERE    p.codigo = :codigo';
        if ($entity->getId()!=null)
            $qry.=' AND p.id != :id';

        $consulta = $em->createQuery($qry);
        $consulta->setParameter('codigo', $entity->getCodigo());
        if ($entity->getId()!=null)
            $consulta->setParameter('id', $entity->getId());

        $filas =  $consulta->getSingleScalarResult();

        if ($filas > 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath('codigo')
                ->setParameter('%string%', $entity->getCodigo())
                ->addViolation();
         }
    }
}




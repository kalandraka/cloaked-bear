<?php

namespace Buseta\CoreBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Component\PropertyAccess\StringUtil;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Security\Core\Util\StringUtils;

/**
 * Class CodigoYPinValidoValidator
 *
 * @package Buseta\CoreBundle\Validator\Constraints
 */
class CodigoYPinValidoValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;


    /**
     * CodigoYPinValidoValidator constructor.
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
     * @param mixed $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($data, Constraint $constraint)
    {
        if ($data->getChofer() === null) {
            return ;
        }

        //codigo barras del chofer
        $choferCodigoBarras = $data->getChofer()->getCodigoBarras();

        //pin del chofer
        $choferPin          = $data->getChofer()->getPin();

        //Validando por codigo de barras que coincidan el código del chofer con el código entrado
        if (!StringUtils::equals($choferCodigoBarras,'') && !StringUtils::equals('',$data->getCodigobarras())) {
            if(StringUtils::equals($choferCodigoBarras, $data->getCodigobarras()))
                return;
            else
                $this->context->addViolationAt('codigobarras', $constraint->messageBarras, array());
        }

        //Validando por pin, solo el chofer o un administrador del sistema
        $dataPin = $data->getPin();
        if (StringUtils::equals($choferPin, $dataPin)) {
            return;
        } else {
            try {
                $admins = $this->em->createQueryBuilder()
                    ->select('u')
                    ->from('HatueySoftSecurityBundle:User','u')
                    //->leftJoin('u.groups','g')
                    //->where('(u.roles LIKE :roleadmin OR g.roles LIKE :roleadmin OR u.roles LIKE :roleasuperdmin OR g.roles LIKE :roleasuperdmin) AND u.pin = :dataPin')
                    ->where('u.pin = :dataPin')
                    //->setParameter('roleadmin','%ROLE_ADMINISTRADOR%')
                    //->setParameter('roleasuperdmin','%ROLE_SUPER_ADMIN%')
                    ->setParameter('dataPin', $dataPin)
                    ->getQuery()
                    ->getResult();
                if(!$admins)
                    $this->context->addViolationAt('pin', $constraint->messagePin, array());
            } catch (NoResultException $e) {
                $this->context->addViolationAt('pin', $constraint->messagePin, array());
            }
            return;
        }
    }
}

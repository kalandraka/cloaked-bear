<?php

namespace Buseta\CombustibleBundle\Validator\Constraints;

use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Buseta\CombustibleBundle\Entity\ConfiguracionCombustible;
use Buseta\CombustibleBundle\Entity\ConfiguracionMarchamo;
use Buseta\CombustibleBundle\Exception\ServicioCombustibleException;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class Marchamo2ValidoValidator
 *
 * @package Buseta\CombustibleBundle\Validator\Constraints
 */
class Marchamo2ValidoValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Logger
     */
    private $logger;


    /**
     * Marchamo1ValidoValidator constructor.
     *
     * @param EntityManager $em
     * @param Logger $logger
     */
    function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $data       The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @throws ServicioCombustibleException
     *
     * @api
     */
    public function validate($data, Constraint $constraint)
    {
        /** @var ConfiguracionCombustible $confCombustible */
        $confCombustible = $data->getCombustible();
        if (null === $confCombustible) {
            throw ServicioCombustibleException::UndefinedCombustibleConfiguration();
        }
        /** @var ConfiguracionMarchamo $confMarchamo */
        $confMarchamo = $this->em->getRepository('BusetaCombustibleBundle:ConfiguracionMarchamo')
            ->getActiveConfiguration();
        if (null === $confMarchamo) {
            throw ServicioCombustibleException::UndefinedMarchamoConfiguration();
        }

        /** @var integer $marchamo2 */
        $marchamo2 = $data->getMarchamo2();
        if ($marchamo2 === null) {
            return ;
        }

        $bodegaChecker = new FuncionesExtras();

        try {
            $marchamoProduct = $confMarchamo->getProducto();
            $bodega = $confMarchamo->getBodega();
            $result = $bodegaChecker->comprobarCantProductoAlmacen($marchamoProduct, $bodega, 1, $this->em);
            if ($result === 'No existe' || $result <= 0) {
                $this->context->buildViolation($constraint->messageCantidad)
                    ->atPath('marchamo2')
                    ->addViolation();
            }

            $serialResult = $bodegaChecker
                ->comprobarCantProductoSeriadoAlmacen($marchamoProduct, $marchamo2, $bodega, $this->em);
            if ($serialResult === 'No existe' || $serialResult <= 0) {
                $this->context->buildViolation($constraint->messageSerial)
                    ->setParameter('%serial%', $marchamo2)
                    ->atPath('marchamo2')
                    ->addViolation();
            }
        } catch (\Exception $e) {
            $this->logger->critical(sprintf(
                'Ha ocurrido un error validando los datos para Marchamo2. Detalles: {message: %s, file: %s, line: %d}',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));

            $this->context->buildViolation('No es posible validar el Marchamo2.')
                ->addViolation();
        }
    }
}

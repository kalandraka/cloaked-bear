<?php

namespace Buseta\TransitoBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Transito controller.
 *
 * @Route("/transito")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Tránsito", routeName="transito_principal")
 */
class TransitoController extends Controller
{
    /**
     * Principal action for main view
     */
    public function principalAction()
    {
        return $this->render('BusetaTransitoBundle:Default:index.html.twig');
    }

}

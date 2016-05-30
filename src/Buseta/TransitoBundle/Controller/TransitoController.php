<?php

namespace Buseta\TransitoBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Transito controller.
 *
 * @Route("/transito")
 *
 */
class TransitoController extends Controller
{
    /**
     * Principal action for main view
     *
     * @Route("/", name="transito")
     */
    public function principalAction()
    {
        return $this->render('BusetaTransitoBundle:Default:index.html.twig');
    }
}

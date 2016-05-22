<?php

namespace HatueySoft\DateTimeBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use HatueySoft\DateTimeBundle\Entity\FechaSistema;
use HatueySoft\DateTimeBundle\Form\Type\FechaSistemaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class FechaSistemaController
 *
 * @package HatueySoft\SysteDateTimeBundle\Controller
 *
 * @Route("/fechasistema")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo Seguridad", routeName="security_usuario")
 * @Breadcrumb(title="Configuración Fecha de Sistema")
 */
class FechaSistemaController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @Route("/", name="hatueysoft_datetime_fechasistema")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $username = $this->get('security.token_storage')->getToken()->getUsername();

        $fechaSistemaConfig = $em->getRepository('HatueySoftDateTimeBundle:FechaSistema')->getUserConfig($username);
        if ($fechaSistemaConfig !== null && $fechaSistemaConfig->isActivo()) {
            $form = $this->createForm(new FechaSistemaType(), $fechaSistemaConfig);
        } else {
            $form = $this->createForm(new FechaSistemaType());
        }

        return $this->render('@HatueySoftDateTime/FechaSistema/index.html.twig',array(
            'form' => $form->createView(),
            'entity' => $fechaSistemaConfig,
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @Route("/update", name="hatueysoft_datetime_fechasistema_update")
     * @Method({"PUT", "POST"})
     */
    public function updateAction(Request $request)
    {
        $form = $this->createForm(new FechaSistemaType());

        $form->submit($request);
        if($form->isSubmitted() && $form->isValid()){
            $fechaSistemaManager = $this->get('hatueysoft.fecha_sistema.manager');

            if ($form->get('activo')->getData()
                && $fechaSistemaConfig = $fechaSistemaManager->setFechaSistema($form->get('fecha')->getData())
            ) {
                $this->get('session')->getFlashBag()->set('success', sprintf(
                    'Se encuentra activa la fecha "%s" para el usuario "%s" en el sistema.',
                    date_format($fechaSistemaConfig->getFecha(), 'd/m/Y'),
                    $fechaSistemaConfig->getUsername()
                ));

                return $this->redirect($this->generateUrl('hatueysoft_datetime_fechasistema'));
            } else {
                $fechaSistemaManager->setFechaSistema(null);

                $this->get('session')->getFlashBag()->set('success', 'Se ha desactivado la fecha del sistema.');

                return $this->redirect($this->generateUrl('hatueysoft_datetime_fechasistema'));
            }
        }

        return $this->render('@HatueySoftDateTime/FechaSistema/index.html.twig',array(
            'form' => $form->createView(),
        ));
    }

}

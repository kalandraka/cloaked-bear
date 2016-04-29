<?php

namespace HatueySoft\DateTimeBundle\Controller;

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

        $fechaSistemaConfig = $em->getRepository('HatueySoftDateTimeBundle:FechaSistema')->findAll();
        if(count($fechaSistemaConfig) == 1)
            $fechaSistemaConfig = $fechaSistemaConfig[0];
        elseif(count($fechaSistemaConfig) == 0)
            $fechaSistemaConfig = new FechaSistema();
        else
            throw new \Exception('No pueden existir más de una configuración para fecha de sistema');

        $form = $this->createForm(new FechaSistemaType(), $fechaSistemaConfig);

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
        $em = $this->getDoctrine()->getManager();

        $fechaSistemaConfig = $em->getRepository('HatueySoftDateTimeBundle:FechaSistema')->findAll();
        if(count($fechaSistemaConfig) == 1)
            $fechaSistemaConfig = $fechaSistemaConfig[0];
        elseif(count($fechaSistemaConfig) == 0)
            $fechaSistemaConfig = new FechaSistema();
        else
            throw new \Exception('No pueden existir más de una configuración para fecha de sistema');

        $form = $this->createForm(new FechaSistemaType(), $fechaSistemaConfig);
        $form->submit($request);

        if($form->isValid()){

            if($fechaSistemaConfig->getActivo() && ($fechaSistemaConfig->getFecha() == null || $fechaSistemaConfig->getFecha() == ''))
            {
                $form->addError(new FormError('No puede establecer como activa la configuración de fecha del sistema sin seleccionar una fecha'));
            }
            else
            {
                if($fechaSistemaConfig->getActivo())
                {
                    $msg = 'Se han salvado los cambios satisfactoriamente. La fecha se encuentra "Activa".';
                }
                else
                {
                    $fechaSistemaConfig->setFecha(null);
                    $msg = 'Se han salvado los cambios satisfactoriamente. La fecha se encuentra "No activa".';
                }

                //persisitiendo los datos
                $em->persist($fechaSistemaConfig);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success',$msg);

                return $this->redirect($this->generateUrl('hatueysoft_datetime_fechasistema'));
            }
        }

        return $this->render('@HatueySoftDateTime/FechaSistema/index.html.twig',array(
                'form' => $form->createView(),
                'entity' => $fechaSistemaConfig,
            ));
    }

}

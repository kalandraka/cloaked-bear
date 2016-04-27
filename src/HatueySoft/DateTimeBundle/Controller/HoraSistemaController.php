<?php

namespace HatueySoft\DateTimeBundle\Controller;

use HatueySoft\DateTimeBundle\Entity\HoraSistema;
use HatueySoft\DateTimeBundle\Form\Type\HoraSistemaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class HoraController
 *
 * @package HatueySoft\DateTimeBundle\Controller
 *
 * @Route("/horasistema")
 * @Security("has_role('ROLE_ADMIN')")
 */
class HoraSistemaController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     *
     * @Route("/", name="hatueysoft_datetime_horasistema")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cambioHoraConfig = $em->getRepository('HatueySoftDateTimeBundle:HoraSistema')->findAll();
        if(count($cambioHoraConfig) == 1)
            $cambioHoraConfig = $cambioHoraConfig[0];
        elseif(count($cambioHoraConfig) == 0)
            $cambioHoraConfig = new HoraSistema();
        else
            throw new \Exception('No pueden existir más de una configuración para cambio de hora en el sistema');

        $form = $this->createForm(new HoraSistemaType(), $cambioHoraConfig);

        return $this->render('@HatueySoftDateTime/HoraSistema/index.html.twig',array(
                'form'      => $form->createView(),
                'entity'    => $cambioHoraConfig,
            ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     *
     * @Route("/update", name="hatueysoft_datetime_horasistema_update")
     * @Method({"PUT", "POST"})
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cambioHoraConfig = $em->getRepository('HatueySoftDateTimeBundle:HoraSistema')->findAll();
        if(count($cambioHoraConfig) == 1)
            $cambioHoraConfig = $cambioHoraConfig[0];
        elseif(count($cambioHoraConfig) == 0)
            $cambioHoraConfig = new HoraSistema();
        else
            throw new \Exception('No pueden existir más de una configuración para cambio de hora en el sistema');

        $form = $this->createForm(new HoraSistemaType(), $cambioHoraConfig);

        $form->submit($request);
        if($form->isValid())
        {
            if($cambioHoraConfig->getActivo() && ($cambioHoraConfig->getHora() == null || $cambioHoraConfig->getHora() == ''))
            {
                $form->addError(new FormError('No puede establecer como activa la configuración de cambio de hora en el sistema sin seleccionar una hora válida'));
            }
            else
            {
                if($cambioHoraConfig->getActivo())
                {
                    $msg = sprintf('Se han salvado los cambios satisfactoriamente. El sistema establece el horario %s para el Servicio de Combustible',date_format($cambioHoraConfig->getHora(),'h:i a'));
                }
                else
                {
                    $cambioHoraConfig->setHora(null);
                    $msg = 'Se han salvado los cambios satisfactoriamente. El horario de cambio ahora se encuentra "Desactivado".';
                }

                //persisitiendo los datos
                $em->persist($cambioHoraConfig);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', $msg);

                return $this->redirect($this->generateUrl('hatueysoft_datetime_horasistema'));
            }
        }

        return $this->render('@HatueySoftDateTime/HoraSistema/index.html.twig',array(
            'form'      => $form->createView(),
            'entity'    => $cambioHoraConfig,
        ));
    }

}

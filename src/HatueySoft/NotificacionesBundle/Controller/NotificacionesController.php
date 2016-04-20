<?php

namespace HatueySoft\NotificacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notificaciones controller.
 *
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Listado de Notificaciones", routeName="notificaciones_list")
 */
class NotificacionesController extends Controller
{

    /**
     * Lists all Notificaciones entities.
     */
    public function listAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $usuario                = $user = $this->get('security.token_storage')->getToken()->getUser();
        $notificacionesManager  = $this->get('notificaciones.interna.manager');
        $paginator              = $this->get('knp_paginator');

        $notificaciones = $notificacionesManager->getNotificacionesUsuario($usuario);

        $notificaciones = $paginator->paginate(
            $notificaciones,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/,
            array('pageParameterName' => 'page')
        );

        return $this->render('HatueySoftNotificacionesBundle:Notificaciones:list.html.twig',array(
           'notificaciones' => $notificaciones,
        ));
    }

    public function countNotificacionesAction()
    {
        $usuario                = $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($usuario instanceof \Symfony\Component\Security\Core\User\User) {
            return new Response();
        }
        $notificacionesManager  = $this->get('notificaciones.interna.manager');

        $cantNotificaciones = $notificacionesManager->countNotificacionesUsuario($usuario, true);

        return $this->render('@HatueySoftNotificaciones/Notificaciones/notificaciones.html.twig', array(
            'cantNotificaciones' => $cantNotificaciones,
        ));
    }

    /**
     * Muestra una notificación.
     * @Breadcrumb(title="Ver Notificación", routeName="notificaciones_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->find('HatueySoftNotificacionesBundle:Notificacion',(int)$id);
        if(!$entity)
            throw $this->createNotFoundException('No se encuentra la notificación con id: '.$id);

        dump($entity);
        $entity->setLeido(true);
        $em->flush();
        return $this->render('HatueySoftNotificacionesBundle:Notificaciones:show.html.twig',array(
            'notificacion' => $entity,
        ));
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = (int)$request->request->get('notificacion_id');

        $entity = $em->find('HatueySoftNotificacionesBundle:Notificacion', $id);
        if(!$entity)
            throw $this->createNotFoundException('No se encuentra la notificación con id: ' . $id);

        //obteniendo notificaciones interna manager
        $notificacionManager = $this->get('notificaciones.interna.manager');

        if($notificacionManager->cambiarEliminado($entity)){
            $this->get('session')->getFlashBag()->set('success', 'Se ha eliminado la notificación de forma satisfactoria.');
        }else{
            $this->get('session')->getFlashBag()->set('error', 'No se ha podido eliminar la notificación.');
        }

        return $this->redirect($this->generateUrl('notificaciones_list'));
    }

}

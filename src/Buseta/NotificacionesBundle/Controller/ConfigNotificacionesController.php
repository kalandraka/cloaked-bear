<?php

namespace Buseta\NotificacionesBundle\Controller;

use Buseta\NotificacionesBundle\Entity\ConfigNotificaciones;
use Buseta\NotificacionesBundle\Form\Models\ConfigNotificacionesCustom;
use Buseta\NotificacionesBundle\NotificacionesVars;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * ConfigNotificaciones controller.
 *
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Lista de tipos de notificaciones", routeName="notificaciones_config_index")
 */
class ConfigNotificacionesController extends Controller
{
    private $defaultNotificacion;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Lista los tipos de notificaciones definidos.
     */
    public function indexAction()
    {
        //$this->em = $this->getDoctrine()->getManager();
        $this->defaultNotificacion = NotificacionesVars::getDefaultNotificacion();

        //cargando configuraciones...
        //$configuraciones = $this->loadConfiguraciones();

        //$forms = $this->buildConfiguracionesForms($configuraciones);

        return $this->render('@BusetaNotificaciones/ConfigNotificaciones/index.html.twig', array(
            'defaultNotificacion' => $this->defaultNotificacion,
            //'forms' => $forms,
        ));
    }

    /**
     * Permite editar un tipo de notificación.
     * @Breadcrumb(title="Editar Tipo de Notificación", routeName="notificaciones_config_edit", routeParameters={"codigo"})
     */
    public function editAction($codigo)
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->defaultNotificacion = NotificacionesVars::getDefaultNotificacion();

        //cargando configuración...
        $configuracion = $this->loadConfiguracion($codigo);

        $form = $this->buildConfiguracionForm($configuracion);

        return $this->render('@BusetaNotificaciones/ConfigNotificaciones/edit.html.twig',array(
                'defaultNotificacion' => $this->defaultNotificacion,
                'codigo' => $codigo,
                'form' => $form,
            ));
    }

    public function updateAction($codigo, Request $request)
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->defaultNotificacion = NotificacionesVars::getDefaultNotificacion();

        //obteniendo configuración guardada
        $entity = $this->em->getRepository('BusetaNotificacionesBundle:ConfigNotificaciones')->findOneByCodigo($codigo);

        if(!$entity)
            $entity = new ConfigNotificaciones($codigo, $this->defaultNotificacion[$codigo]);

        $custom = new ConfigNotificacionesCustom($entity);
        $form = $this->createForm('notificaciones_config_type',$custom);

        $form->submit($request);
        if($form->isValid()){
            try{
                //salvando datos de la entidad
                $entity->setAsunto($custom->getAsunto());
                $entity->setCorreosDefinidos($custom->getCorreosDefinidos());
                $entity->setUsuariosDefinidos($custom->getUsuariosDefinidos());
                $entity->setNotificacionCorreo($custom->getNotificacionCorreo());
                $entity->setNotificacionInterna($custom->getNotificacionInterna());
                $entity->setActivo($custom->getActivo());

                $this->em->persist($entity);
                $this->em->flush();

                $this->get('session')->getFlashBag()->set('success',sprintf('Se han salvado los datos para la configuración "%s" de forma satisfactoria.',$this->defaultNotificacion[$codigo]));

                return $this->redirect($this->generateUrl('notificaciones_config_index'));
            }catch (\Exception $e){
                $this->get('session')->getFlashBag()->set('error','Ha ocurrido un error inesperado y no se han podido salvar los datos.');
            }
        }

        $form = $this->buildConfiguracionForm($this->loadConfiguracion($codigo));

        return $this->render('@BusetaNotificaciones/ConfigNotificaciones/edit.html.twig',array(
            'defaultNotificacion' => $this->defaultNotificacion,
            'codigo' => $codigo,
            'form' => $form,
        ));
    }


    /**
     * Devuelve las configuraciones salvadas para las diferentes notificaciones
     * @return array
     */
    private function loadConfiguraciones()
    {
        try{
            $configuraciones = $this->em->getRepository('BusetaNotificacionesBundle:ConfigNotificaciones')->findAll();

            foreach($this->defaultNotificacion as $code => $name){
                $flag = false;
                foreach($configuraciones as $config){
                    if($config->getCodigo() == $code){
                        $flag = true;
                        break;
                    }
                }
                if(!$flag)
                    $configuraciones[] = new ConfigNotificaciones($code, $name);
            }

        }catch (NoResultException $e){
            foreach($this->defaultNotificacion as $code => $name){
                $configuraciones[] = new ConfigNotificaciones($code, $name);
            }
        }

        return $configuraciones;
    }

    /**
     * Devuelve la configuración para el código pasado por parámetros
     * @param $codigo
     * @return ConfigNotificaciones
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function loadConfiguracion($codigo)
    {
        try
        {
            $configuracion = $this->em->getRepository('BusetaNotificacionesBundle:ConfigNotificaciones')->findOneBy(
                array(
                    'codigo' => $codigo,
                )
            );

            if(!$configuracion)
            {
                if($this->defaultNotificacion[$codigo])
                    $configuracion = new ConfigNotificaciones($codigo, $this->defaultNotificacion[$codigo]);
                else
                    throw $this->createNotFoundException('No existe una notificación con código: '.$codigo);
            }
        }
        catch (NoResultException $e)
        {
            $configuracion = new ConfigNotificaciones($codigo, $this->defaultNotificacion[$codigo]);
        }

        return $configuracion;
    }

    /**
     * Devuelve los formularios para las configuraciones establecidas en el sistema
     * @param $configuraciones
     * @return array
     */
    private function buildConfiguracionesForms($configuraciones)
    {
        $forms = array();

        //recorriendo las configuraciones
        foreach($configuraciones as $config){
            /* @var ConfigNotificaciones $config */
            $customConfig = new ConfigNotificacionesCustom($config);

            //creando los formularios con las configuraciones entradas por parámetros
            $forms[$config->getCodigo()] = $this->createForm('notificaciones_config_type', $customConfig)->createView();
        }

        return $forms;
    }

    /**
     * Cargando la configuración de la notificación en el formulario
     * @param $configuracion
     * @return \Symfony\Component\Form\FormView
     */
    private function buildConfiguracionForm($configuracion)
    {
        $customConfig = new ConfigNotificacionesCustom($configuracion);
        $form = $this->createForm('notificaciones_config_type', $customConfig)->createView();

        return $form;
    }
}

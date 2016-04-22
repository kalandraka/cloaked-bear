<?php

namespace Buseta\NotificacionesBundle\Manager;

use Buseta\NotificacionesBundle\Entity\Notificacion;
use Buseta\NotificacionesBundle\NotificacionesVars;
use Doctrine\ORM\EntityManager;
use HatueySoft\SecurityBundle\Entity\User as Usuario;
use Doctrine\ORM\NoResultException;

class NotificacionesManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var NotificacionesInternaManager
     */
    private $internaManager;

    /**
     * @var NotificacionesCorreoManager
     */
    private $correoManager;

    /**
     * @var array
     */
    private $defaultNotificacion;

    function __construct(EntityManager $em, NotificacionesInternaManager $internaManager, NotificacionesCorreoManager $correoManager)
    {
        $this->em                   = $em;
        $this->internaManager       = $internaManager;
        $this->correoManager        = $correoManager;
        $this->defaultNotificacion  = NotificacionesVars::getDefaultNotificacion();
    }

    /**
     * Genera una notificación genérica a partir de un cuerpo entrado por parámetro
     *
     * @return bool
     */
    public function generateNotificacion($asunto, $cuerpo)
    {
        //notificaciones por correo
        try{
            $this->correoManager->sendCorreos(
                'notificacion_correo',
                array(
                    'asunto' => $asunto,
                    'cuerpo' => $cuerpo,
                ));
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            //notificar en caso de lanzar excepcion
        }

        //notificaciones internas
        try{
            $this->internaManager->sendNotificaciones(
                'notificacion_interna',
                array(
                    'asunto' => $asunto,
                    'cuerpo' => $cuerpo,
                ));
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            //notificar en caso de lanzar excepcion
        }
        return true;
    }

    /**
     * Genera una notificación Interna a partir de un asunto y un cuerpo entrados por parámetro
     *
     * @return bool
     */
    public function generateNotificacionInterna($asunto, $cuerpo)
    {
        //notificaciones internas
        try{
            $this->internaManager->sendNotificaciones(
                'notificacion_interna',
                array(
                    'asunto' => $asunto,
                    'cuerpo' => $cuerpo,
                ));
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            //notificar en caso de lanzar excepcion
        }
        return true;
    }

    /**
     * Genera una notificación por correo a partir de un asunto y un cuerpo entrados por parámetro
     *
     * @return bool
     */
    public function generateNotificacionCorreo($asunto, $cuerpo)
    {
        //notificaciones por correo
        try{
            $this->correoManager->sendCorreos(
                'notificacion_correo',
                array(
                    'asunto' => $asunto,
                    'cuerpo' => $cuerpo,
                ));
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            //notificar en caso de lanzar excepcion
        }
        return true;
    }

}

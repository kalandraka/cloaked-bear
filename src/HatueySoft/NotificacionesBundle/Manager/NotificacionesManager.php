<?php

namespace HatueySoft\NotificacionesBundle\Manager;

use HatueySoft\NotificacionesBundle\NotificacionesVars;
use Doctrine\ORM\EntityManager;

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
     * @var NotificacionesSMSManager
     */
    private $smsManager;

    /**
     * @var array
     */
    private $defaultNotificacion;

    function __construct(EntityManager $em, NotificacionesInternaManager $internaManager, NotificacionesCorreoManager $correoManager, NotificacionesSMSManager $smsManager)
    {
        $this->em                   = $em;
        $this->internaManager       = $internaManager;
        $this->correoManager        = $correoManager;
        $this->smsManager           = $smsManager;
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

        //notificaciones por sms
        try{
            $this->smsManager->sendSMS('notificacion_sms', $cuerpo);
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

    /**
     * Genera una notificación por sms a partir de un mensaje entrado por parámetro
     *
     * @return bool
     */
    public function generateNotificacionSMS($cuerpo)
    {
        //notificaciones por sms
        try{
            $this->smsManager->sendSMS('notificacion_sms', $cuerpo);
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            //notificar en caso de lanzar excepcion
        }
        return true;
    }

}

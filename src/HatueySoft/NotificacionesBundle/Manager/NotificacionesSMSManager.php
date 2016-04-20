<?php
/**
 * Created by PhpStorm.
 * User: admarin
 * Date: 16/04/16
 * Time: 9:33
 */

namespace HatueySoft\NotificacionesBundle\Manager;

use HatueySoft\NotificacionesBundle\Entity\NotificacionSMS;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use HatueySoft\NotificacionesBundle\Entity\ConfigNotificaciones;
use HatueySoft\NotificacionesBundle\NotificacionesVars;

class NotificacionesSMSManager
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var array $defaultNotificacion
     */
    private $defaultNotificacion;

    /**
     * @param EntityManager $em
     */
    function __construct(EntityManager $em, Container $container)
    {
        $this->em                   = $em;
        $this->container            = $container;
        $this->defaultNotificacion  = NotificacionesVars::getDefaultNotificacion();
    }

    /**
     * @param $codigoNotif
     * @param $cuerpo
     * @return bool
     * @throws \Exception
     */
    public function sendSMS($codigoNotif, $cuerpo)
    {
        /* @var ConfigNotificaciones $config */
        $config = $this->em->getRepository('HatueySoftNotificacionesBundle:ConfigNotificaciones')->findOneByCodigo($codigoNotif);
        if(!$config)
            return false;

        $args = array();
        if($config->getActivo() && $config->getNotificacionSMS())
        {

            //usuario
            $args['u'] = $this->container->getParameter('notificaciones.sms.user');

            //token de seguridad
            $args['h'] = $this->container->getParameter('notificaciones.sms.token');

            $args['to'] = implode(',', $config->getNumerosDefinidos());

            $args['msg'] = $cuerpo;

            $url = $this->createURL($args);

            $result = null;
            $enviado = false;

            try{
                //Enviar la $url y procesar la respuesta JSON para obtener el codigo de estado
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);

                if (false == $response){
                    $enviado = false;
                    throw new \Exception(sprintf('No se pudo establecer conexión con la url proporcionada'));
                }
                $result = json_decode($response);
            }catch (\Exception $ex)
            {
                $enviado = false;
                //print_r($ex->getMessage());exit;
                //notificar en caso de lanzar excepcion
            }

            if('OK' == $result['status']) {
                $enviado = true;
                //throw new \Exception(sprintf('No se pudo enviar el sms'));
            }

            //Salvar la notificacion
            $notif = new NotificacionSMS();
            $notif->setCodigo($codigoNotif);
            $notif->setNumerosNotificados($config->getNumerosDefinidos());
            $notif->setMensaje($cuerpo);
            $notif->setEnviado($enviado);
            $this->em->persist($notif);
            $this->em->flush();
        }
        return true;
    }

    /**
     * @param array $args
     * @return String
     * @throws \Exception
     */
    private function createURL($args)
    {
        $msg = urlencode($args['msg']);

        $url = $this->container->getParameter('notificaciones.sms.url');
        $url .= '&u=' . $args['u'];
        $url .= '&h=' . $args['h'];
        $url .= '&op=pv&to=' . $args['to'];
        $url .= '&msg=' . $msg;
        return $url;
    }

}
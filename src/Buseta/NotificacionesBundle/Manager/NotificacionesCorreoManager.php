<?php

namespace Buseta\NotificacionesBundle\Manager;


use Buseta\NotificacionesBundle\Entity\ConfigNotificaciones;
use Buseta\NotificacionesBundle\NotificacionesVars;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NotificacionesCorreoManager {

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

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
     * @param TokenStorage $tokenStorage
     * @param TwigEngine $templating
     * @param \Swift_Mailer $mailer
     * @param Container $container
     */
    function __construct(EntityManager $em, TokenStorage $tokenStorage, TwigEngine $templating, \Swift_Mailer $mailer, Container $container)
    {
        $this->em                   = $em;
        $this->tokenStorage = $tokenStorage;
        $this->templating           = $templating;
        $this->mailer               = $mailer;
        $this->container            = $container;
        $this->defaultNotificacion  = NotificacionesVars::getDefaultNotificacion();
    }

    /**
     * @param $codigoNotif
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function sendCorreos($codigoNotif, $params)
    {
        /* @var ConfigNotificaciones $config */
        $config = $this->em->getRepository('BusetaNotificacionesBundle:ConfigNotificaciones')->findOneByCodigo($codigoNotif);
        if(!$config)
            return false;

        $args = array();
        if($config->getActivo() && $config->getNotificacionCorreo())
        {
            //Estableciendo asunto del correo
            if($params['asunto'] != '' || $params['asunto'] != null)
                $args['subject'] = $params['asunto'];
            else if($config->getAsunto())
                $args['subject'] = $config->getAsunto();
            else
                $args['subject'] = $this->defaultNotificacion[$codigoNotif];

            //variable from
            $args['from'] = $this->container->getParameter('notificaciones.email.from');

            //codigo
            $args['codigo'] = $codigoNotif;

            $notsend = 0;
            foreach($config->getCorreosDefinidos() as $email)
            {
                $args['to'] = $email;
                if(!$this->createMailInstance($args, $params)){
                    $notsend++;
                }
            }

            if($notsend)
                throw new \Exception(sprintf('No se pudo enviar %d correo%s', $notsend, ($notsend > 0) ? 's':''));
        }
        return true;
    }

    /**
     * @param array $args
     * @param array $params
     * @return bool
     */
    private function createMailInstance($args, $params)
    {
        try{
            $message = \Swift_Message::newInstance()
                ->setSubject($args['subject'])
                ->setFrom($args['from'])
                ->setTo($args['to'])
                ->setContentType('text/html')
                ->setBody(
                    $this->templating->render(
                        sprintf('BusetaNotificacionesBundle:CorreoTemplates:%s_template.html.twig', $args['codigo']),
                        $params
                    )
                );
            $this->mailer->send($message);

            return true;
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            return false;
        }
    }

} 
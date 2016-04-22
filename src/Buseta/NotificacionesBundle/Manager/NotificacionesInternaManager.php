<?php

namespace Buseta\NotificacionesBundle\Manager;

use Buseta\NotificacionesBundle\Entity\ConfigNotificaciones;
use Buseta\NotificacionesBundle\Entity\Notificacion;
use Buseta\NotificacionesBundle\NotificacionesVars;
use HatueySoft\SecurityBundle\Entity\User as Usuario;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Security\Core\Authentication;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificacionesInternaManager {

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
     * @var array $defaultNotificacion
     */
    private $defaultNotificacion;

    /**
     * @param $em
     * @param $tokenStorage
     * @param $templating
     */
    function __construct(EntityManager $em, TokenStorage $tokenStorage, TwigEngine $templating)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->templating = $templating;
        $this->defaultNotificacion  = NotificacionesVars::getDefaultNotificacion();
    }

    /**
     * @param $codigoNotif
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function sendNotificaciones($codigoNotif, $params)
    {
        /* @var ConfigNotificaciones $config */
        $config = $this->em->getRepository('BusetaNotificacionesBundle:ConfigNotificaciones')->findOneByCodigo($codigoNotif);
        if(!$config)
            return false;

        if($config->getActivo() && $config->getNotificacionInterna())
        {
            //Estableciendo asunto del correo
            if($params['asunto'] != '' || $params['asunto'] != null)
                $asunto = $params['asunto'];
            else if($config->getAsunto())
                $asunto = $config->getAsunto();
            else
                $asunto = $this->defaultNotificacion[$codigoNotif];
            $notsend = 0;
            foreach($config->getUsuariosDefinidos() as $username)
            {
                $systemUser = $this->em->getRepository('HatueySoftSecurityBundle:User')->findOneByUsername($username);
                if(!$this->createNotificacionInstance($asunto, $codigoNotif, $systemUser, $params)){
                    $notsend++;
                }
            }

            //salvando las notificaciones
            $this->em->flush();

            if($notsend)
                throw new \Exception(sprintf('No se pudo enviar %d notificaci%s', $notsend, ($notsend > 0) ? 'ones':'ón'));
        }
        return true;

    }

    /**
     * @param $asunto
     * @param $codigo
     * @param $usuario
     * @param string $tipo
     * @param $params
     * @return bool
     */
    public function createNotificacionInstance($asunto, $codigo , $usuario, $params, $tipo='info')
    {
        try{
            $notif = new Notificacion();

            $notif->setAsunto($asunto);
            $notif->setCodigo($codigo);
            $notif->setUsuario($usuario);
            $notif->setMensaje($this->templating->render(
                sprintf('BusetaNotificacionesBundle:InternaTemplates:%s_template.html.twig', $codigo),
                $params));
            $notif->setTipo($tipo);

            $this->em->persist($notif);

            return true;
        }catch (\Exception $e){
            //print_r($e->getMessage());exit;
            return false;
        }
    }

    public function cambiarEliminado($notificacion)
    {
        try{
            if($notificacion instanceof Notificacion)
            {
                $notificacion->setDeleted(new \DateTime());
                $this->em->persist($notificacion);
            }
            elseif(is_integer($notificacion))
            {
                $entity = $this->em->find('BusetaNotificacionesBundle:Notificacion', $notificacion);
                $entity->setDeleted(new \DateTime());
                $this->em->persist($entity);
            }

            $this->em->flush();

            return true;
        }catch (\Exception $e){
            return false;
        }

    }

    public function cambiarLeido($notificacion)
    {
        if($notificacion instanceof Notificacion)
        {
            $notificacion->setLeido(true);
            $this->em->persist($notificacion);
        }
        elseif(is_integer($notificacion))
        {
            $entity = $this->em->find('BusetaNotificacionesBundle:Notificacion', $notificacion);
            $entity->setLeido(true);
            $this->em->persist($entity);
        }
        $this->em->flush();
    }

    public function getNotificacionesUsuario(Usuario $usuario){
        if ($usuario->getUsername() != 'admin') {
            $dql = 'SELECT n FROM BusetaNotificacionesBundle:Notificacion n INNER JOIN n.usuario u WHERE n.deleted IS NULL AND u.id = :usuarioid ORDER BY n.created DESC';

            $query = $this->em->createQuery($dql)->setParameter('usuarioid', $usuario->getId());
            try {
                return $query->getResult();
            } catch (NoResultException $e) {
                return array();
            }
        }
        else{
            return array();
        }
    }

    public function countNotificacionesUsuario(UserInterface $usuario)
    {
        if ($usuario->getUsername() != 'admin'){
            $dql = 'SELECT COUNT(n.id) FROM BusetaNotificacionesBundle:Notificacion n INNER JOIN n.usuario u WHERE n.deleted IS NULL AND u.id = :usuarioid';
            $query = $this->em->createQuery($dql)
                ->setParameter('usuarioid', $usuario->getId());
            try{
                return $query->getSingleScalarResult();
            }catch (NoResultException $e) {
                return 0;
            }
        }else{
            return 0;
        }

    }

} 
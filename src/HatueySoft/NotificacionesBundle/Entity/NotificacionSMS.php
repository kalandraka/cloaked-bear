<?php

namespace HatueySoft\NotificacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificacionSMS
 *
 * @ORM\Table(name="notif_notificaciones_sms")
 * @ORM\Entity(repositoryClass="HatueySoft\NotificacionesBundle\Entity\Repository\NotificacionSMSRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class NotificacionSMS
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;

    /**
     * @var array
     *
     * @ORM\Column(name="numerosNotificados", type="array")
     */
    private $numerosNotificados;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text")
     */
    private $mensaje;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enviado", type="boolean")
     */
    private $enviado;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NotificacionSMS
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set numerosNotificados
     *
     * @param array $numerosNotificados
     * @return NotificacionSMS
     */
    public function setNumerosNotificados($numerosNotificados)
    {
        $this->numerosNotificados = $numerosNotificados;
    
        return $this;
    }

    /**
     * Get numerosNotificados
     *
     * @return array 
     */
    public function getNumerosNotificados()
    {
        return $this->numerosNotificados;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return NotificacionSMS
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     * @return NotificacionSMS
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    
        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set leido
     *
     * @param boolean $enviado
     * @return NotificacionSMS
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;

        return $this;
    }

    /**
     * Get enviado
     *
     * @return boolean
     */
    public function getEnviado()
    {
        return $this->enviado;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->created = new \DateTime();
    }
}

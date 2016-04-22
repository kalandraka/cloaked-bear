<?php

namespace Buseta\NotificacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificacionInterna
 *
 * @ORM\Table(name="notif_notificaciones_internas")
 * @ORM\Entity(repositoryClass="Buseta\NotificacionesBundle\Entity\Repository\NotificacionInternaRepository")
 */
class NotificacionInterna
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
     * @ORM\Column(name="usuariosNotificados", type="array")
     */
    private $usuariosNotificados;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime")
     */
    private $deleted;

    /**
     * @var string
     *
     * @ORM\Column(name="asunto", type="string", length=255)
     */
    private $asunto;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text")
     */
    private $mensaje;


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
     * @return NotificacionInterna
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
     * Set usuariosNotificados
     *
     * @param array $usuariosNotificados
     * @return NotificacionInterna
     */
    public function setUsuariosNotificados($usuariosNotificados)
    {
        $this->usuariosNotificados = $usuariosNotificados;
    
        return $this;
    }

    /**
     * Get usuariosNotificados
     *
     * @return array 
     */
    public function getUsuariosNotificados()
    {
        return $this->usuariosNotificados;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return NotificacionInterna
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
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return NotificacionInterna
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set asunto
     *
     * @param string $asunto
     * @return NotificacionInterna
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    
        return $this;
    }

    /**
     * Get asunto
     *
     * @return string 
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     * @return NotificacionInterna
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
}

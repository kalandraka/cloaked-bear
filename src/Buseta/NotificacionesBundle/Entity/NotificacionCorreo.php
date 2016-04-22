<?php

namespace Buseta\NotificacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificacionCorreo
 *
 * @ORM\Table(name="notif_notificaciones_correo")
 * @ORM\Entity(repositoryClass="Buseta\NotificacionesBundle\Entity\Repository\NotificacionCorreoRepository")
 */
class NotificacionCorreo
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
     * @ORM\Column(name="correosEnviados", type="array")
     */
    private $correosEnviados;

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
     * @return NotificacionCorreo
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
     * Set correosEnviados
     *
     * @param array $correosEnviados
     * @return NotificacionCorreo
     */
    public function setCorreosEnviados($correosEnviados)
    {
        $this->correosEnviados = $correosEnviados;
    
        return $this;
    }

    /**
     * Get correosEnviados
     *
     * @return array 
     */
    public function getCorreosEnviados()
    {
        return $this->correosEnviados;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return NotificacionCorreo
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
     * @return NotificacionCorreo
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
     * @return NotificacionCorreo
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
     * @return NotificacionCorreo
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

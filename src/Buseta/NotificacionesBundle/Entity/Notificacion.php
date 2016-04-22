<?php

namespace Buseta\NotificacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Notificacion
 *
 * @ORM\Table(name="notif_notificaciones")
 * @ORM\Entity(repositoryClass="Buseta\NotificacionesBundle\Entity\Repository\NotificacionRepository")
 */
class Notificacion
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
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\HatueySoft\SecurityBundle\Entity\User")
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=32)
     * @Assert\Choice(choices={"info","alert","error","success"})
     */
    private $tipo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="leido", type="boolean")
     */
    private $leido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;


    function __construct()
    {
        $this->leido = false;
        $this->created = new \DateTime();
    }


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
     * Set asunto
     *
     * @param string $asunto
     * @return Notificacion
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
     * @return Notificacion
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
     * Set tipo
     *
     * @param string $tipo
     * @return Notificacion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set leido
     *
     * @param boolean $leido
     * @return Notificacion
     */
    public function setLeido($leido)
    {
        $this->leido = $leido;
    
        return $this;
    }

    /**
     * Get leido
     *
     * @return boolean 
     */
    public function getLeido()
    {
        return $this->leido;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Notificacion
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
     * @return Notificacion
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
     * Set usuario
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $usuario
     * @return Notificacion
     */
    public function setUsuario(\HatueySoft\SecurityBundle\Entity\User $usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return Notificacion
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
}
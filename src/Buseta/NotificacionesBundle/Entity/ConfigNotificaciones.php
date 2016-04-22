<?php

namespace Buseta\NotificacionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfigNotificaciones
 *
 * @ORM\Table(name="notif_config_notificaciones")
 * @ORM\Entity(repositoryClass="Buseta\NotificacionesBundle\Entity\Repository\ConfigNotificacionesRepository")
 */
class ConfigNotificaciones
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="asunto", type="string", length=255, nullable=true)
     */
    private $asunto;

    /**
     * @var array
     *
     * @ORM\Column(name="correosDefinidos", type="array", nullable=true)
     */
    private $correosDefinidos;

    /**
     * @var array
     *
     * @ORM\Column(name="usuariosDefinidos", type="array", nullable=true)
     */
    private $usuariosDefinidos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notificacionInterna", type="boolean", nullable=true)
     */
    private $notificacionInterna;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notificacionCorreo", type="boolean", nullable=true)
     */
    private $notificacionCorreo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @var array
     *
     * @ORM\Column(name="horarios", type="array", nullable=true)
     */
    private $horarios;

    /**
     * @var integer
     *
     * @ORM\Column(name="frecuencia", type="integer", nullable=true)
     */
    private $frecuencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\HatueySoft\SecurityBundle\Entity\User")
     */
    private $updatedby;


    function __construct($codigo = null, $nombre = null)
    {
        if($codigo)
            $this->codigo = $codigo;
        if($nombre)
            $this->nombre = $nombre;
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
     * Set nombre
     *
     * @param string $nombre
     * @return ConfigNotificaciones
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return ConfigNotificaciones
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
     * Set correosDefinidos
     *
     * @param array $correosDefinidos
     * @return ConfigNotificaciones
     */
    public function setCorreosDefinidos($correosDefinidos)
    {
        $this->correosDefinidos = $correosDefinidos;
    
        return $this;
    }

    /**
     * Get correosDefinidos
     *
     * @return array 
     */
    public function getCorreosDefinidos()
    {
        return $this->correosDefinidos;
    }

    /**
     * Set usuariosDefinidos
     *
     * @param array $usuariosDefinidos
     * @return ConfigNotificaciones
     */
    public function setUsuariosDefinidos($usuariosDefinidos)
    {
        $this->usuariosDefinidos = $usuariosDefinidos;
    
        return $this;
    }

    /**
     * Get usuariosDefinidos
     *
     * @return array 
     */
    public function getUsuariosDefinidos()
    {
        return $this->usuariosDefinidos;
    }

    /**
     * Set notificacionInterna
     *
     * @param boolean $notificacionInterna
     * @return ConfigNotificaciones
     */
    public function setNotificacionInterna($notificacionInterna)
    {
        $this->notificacionInterna = $notificacionInterna;
    
        return $this;
    }

    /**
     * Get notificacionInterna
     *
     * @return boolean 
     */
    public function getNotificacionInterna()
    {
        return $this->notificacionInterna;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return ConfigNotificaciones
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set horarios
     *
     * @param array $horarios
     * @return ConfigNotificaciones
     */
    public function setHorarios($horarios)
    {
        $this->horarios = $horarios;
    
        return $this;
    }

    /**
     * Get horarios
     *
     * @return array 
     */
    public function getHorarios()
    {
        return $this->horarios;
    }

    /**
     * Set frecuencia
     *
     * @param integer $frecuencia
     * @return ConfigNotificaciones
     */
    public function setFrecuencia($frecuencia)
    {
        $this->frecuencia = $frecuencia;
    
        return $this;
    }

    /**
     * Get frecuencia
     *
     * @return integer 
     */
    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    /**
     * Set activacion
     *
     * @param array $activacion
     * @return ConfigNotificaciones
     */
    public function setActivacion($activacion)
    {
        $this->activacion = $activacion;
    
        return $this;
    }

    /**
     * Get activacion
     *
     * @return array 
     */
    public function getActivacion()
    {
        return $this->activacion;
    }

    /**
     * Set asunto
     *
     * @param string $asunto
     * @return ConfigNotificaciones
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
     * Set notificacionCorreo
     *
     * @param boolean $notificacionCorreo
     * @return ConfigNotificaciones
     */
    public function setNotificacionCorreo($notificacionCorreo)
    {
        $this->notificacionCorreo = $notificacionCorreo;
    
        return $this;
    }

    /**
     * Get notificacionCorreo
     *
     * @return boolean 
     */
    public function getNotificacionCorreo()
    {
        return $this->notificacionCorreo;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return ConfigNotificaciones
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updatedby
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $updatedby
     * @return ConfigNotificaciones
     */
    public function setUpdatedby(\HatueySoft\SecurityBundle\Entity\User $updatedby = null)
    {
        $this->updatedby = $updatedby;
    
        return $this;
    }

    /**
     * Get updatedby
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getUpdatedby()
    {
        return $this->updatedby;
    }
}
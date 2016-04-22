<?php
namespace Buseta\NotificacionesBundle\Form\Models;

class ConfigNotificacionesCustom {

    /**
     * @var string
     */
    private $codigo;

    /**
     * @var string
     */
    private $asunto;

    /**
     * @var array
     */
    private $correosDefinidos;

    /**
     * @var array
     */
    private $usuariosDefinidos;

    /**
     * @var boolean
     */
    private $notificacionInterna;

    /**
     * @var boolean
     */
    private $notificacionCorreo;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var array
     */
    private $horarios;

    /**
     * @var integer
     */
    private $frecuencia;

    /**
     * @param \Buseta\NotificacionesBundle\Entity\ConfigNotificaciones $configNotificacion
     */
    function __construct(\Buseta\NotificacionesBundle\Entity\ConfigNotificaciones $configNotificacion = null)
    {
        if($configNotificacion){
            $this->codigo               = $configNotificacion->getCodigo();
            $this->asunto               = $configNotificacion->getAsunto();
            $this->correosDefinidos     = $configNotificacion->getCorreosDefinidos();
            $this->usuariosDefinidos    = $configNotificacion->getUsuariosDefinidos();
            $this->notificacionInterna  = $configNotificacion->getNotificacionInterna();
            $this->notificacionCorreo   = $configNotificacion->getNotificacionCorreo();
            $this->activo               = $configNotificacion->getActivo();
            $this->horarios             = $configNotificacion->getHorarios();
            $this->frecuencia           = $configNotificacion->getFrecuencia();
        }else
            throw new \Exception('Debe definirse una configuración de notificación.');
    }

    /**
     * @param boolean $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param string $asunto
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    }

    /**
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * @param string $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param array $correosDefinidos
     */
    public function setCorreosDefinidos($correosDefinidos)
    {
        $this->correosDefinidos = $correosDefinidos;
    }

    /**
     * @return array
     */
    public function getCorreosDefinidos()
    {
        return $this->correosDefinidos;
    }

    /**
     * @param int $frecuencia
     */
    public function setFrecuencia($frecuencia)
    {
        $this->frecuencia = $frecuencia;
    }

    /**
     * @return int
     */
    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    /**
     * @param array $horarios
     */
    public function setHorarios($horarios)
    {
        $this->horarios = $horarios;
    }

    /**
     * @return array
     */
    public function getHorarios()
    {
        return $this->horarios;
    }

    /**
     * @param boolean $notificacionCorreo
     */
    public function setNotificacionCorreo($notificacionCorreo)
    {
        $this->notificacionCorreo = $notificacionCorreo;
    }

    /**
     * @return boolean
     */
    public function getNotificacionCorreo()
    {
        return $this->notificacionCorreo;
    }

    /**
     * @param boolean $notificacionInterna
     */
    public function setNotificacionInterna($notificacionInterna)
    {
        $this->notificacionInterna = $notificacionInterna;
    }

    /**
     * @return boolean
     */
    public function getNotificacionInterna()
    {
        return $this->notificacionInterna;
    }

    /**
     * @param array $usuariosDefinidos
     */
    public function setUsuariosDefinidos($usuariosDefinidos)
    {
        $this->usuariosDefinidos = $usuariosDefinidos;
    }

    /**
     * @return array
     */
    public function getUsuariosDefinidos()
    {
        return $this->usuariosDefinidos;
    }


} 
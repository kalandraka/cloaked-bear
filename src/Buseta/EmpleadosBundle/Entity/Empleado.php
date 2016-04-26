<?php

namespace Buseta\EmpleadosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Empleado
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Buseta\EmpleadosBundle\Entity\Repository\EmpleadoRepository")
 */
class Empleado
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
     * @ORM\Column(name="nombres", type="string", length=50)
     */
    private $nombres;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=50)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="cedula", type="string", length=15)
     */
    private $cedula;

    /**
     * @var string
     *
     * @ORM\Column(name="genero", type="string", length=15)
     */
    private $genero;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\EstadoCivil")
     */
    private $estadoCivil;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Nacionalidad")
     */
    private $nacionalidad;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text")
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=25)
     */
    private $telefono;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaNacimiento", type="date")
     */
    private $fechaNacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="pin", type="string", length=15)
     */
    private $pin;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoBarras", type="string", length=50)
     */
    private $codigoBarras;

    /**
     * @var string
     *
     * @ORM\Column(name="hhrr", type="string", length=20)
     */
    private $hhrr;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Buseta\EmpleadosBundle\Entity\TipoEmpleado")
     */
    private $tipoEmpleado;

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
     * Set nombres
     *
     * @param string $nombres
     *
     * @return Empleado
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;

        return $this;
    }

    /**
     * Get nombres
     *
     * @return string
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     *
     * @return Empleado
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set cedula
     *
     * @param string $cedula
     *
     * @return Empleado
     */
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;

        return $this;
    }

    /**
     * Get cedula
     *
     * @return string
     */
    public function getCedula()
    {
        return $this->cedula;
    }

    /**
     * Set genero
     *
     * @param string $genero
     *
     * @return Empleado
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return string
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set estadoCivil
     *
     * @param string $estadoCivil
     *
     * @return Empleado
     */
    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;

        return $this;
    }

    /**
     * Get estadoCivil
     *
     * @return string
     */
    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    /**
     * Set nacionalidad
     *
     * @param string $nacionalidad
     *
     * @return Empleado
     */
    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    /**
     * Get nacionalidad
     *
     * @return string
     */
    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Empleado
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Empleado
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     *
     * @return Empleado
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set pin
     *
     * @param string $pin
     *
     * @return Empleado
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Get pin
     *
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set codigoBarras
     *
     * @param string $codigoBarras
     *
     * @return Empleado
     */
    public function setCodigoBarras($codigoBarras)
    {
        $this->codigoBarras = $codigoBarras;

        return $this;
    }

    /**
     * Get codigoBarras
     *
     * @return string
     */
    public function getCodigoBarras()
    {
        return $this->codigoBarras;
    }

    /**
     * Set hhrr
     *
     * @param string $hhrr
     *
     * @return Empleado
     */
    public function setHhrr($hhrr)
    {
        $this->hhrr = $hhrr;

        return $this;
    }

    /**
     * Get hhrr
     *
     * @return string
     */
    public function getHhrr()
    {
        return $this->hhrr;
    }

    /**
     * @return string
     */
    public function getTipoEmpleado()
    {
        return $this->tipoEmpleado;
    }

    /**
     * @param string $tipoEmpleado
     */
    public function setTipoEmpleado($tipoEmpleado)
    {
        $this->tipoEmpleado = $tipoEmpleado;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->nombres.' '.$this->apellidos;
    }

    public function getNombreCompleto(){
        return $this->nombres . ' ' . $this->apellidos;
    }
}


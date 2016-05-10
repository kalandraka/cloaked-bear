<?php

namespace Buseta\BodegaBundle\Form\Model;

use Buseta\BodegaBundle\Entity\Proveedor;
use Buseta\BodegaBundle\Entity\Tercero;
use HatueySoft\UploadBundle\Entity\UploadResources;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\NomencladorBundle\Entity\Moneda;

class ProveedorModel
{
    /**
     * @var integer
     */
    private $proveedorId;

    /**
     * @var integer
     */
    private $TerceroId;

    /**
     * @var UploadResources
     */
    private $foto;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $alias;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var string
     */
    private $web;

    /**
     * @var string
     */
    private $direccion;

    /**
     * @var string
     */
    private $ciudad;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $apartado;

    /**
     * @var string
     */
    private $pais;

    /**
     * @var string
     */
    private $contacto;

    /**
     * @var string
     */
    private $puesto;

    /**
     * @var string
     */
    private $celular;

    /**
     * @var string
     */
    private $email;

    /**
     * @var Moneda
     *
     * @Assert\NotNull()
     */
    private $moneda;

    /**
     * @var int
     */
    private $creditoLimite;

    /**
     * @var string
     */
    private $cifNif;

    /**
     * @var string
     */
    private $observaciones;

    /**
     * @var string
     */
    private $pago;

    /**
     * @var array
     */
    private $marcas;


    public function __construct(Proveedor $proveedor = null)
    {
        if ($proveedor === null) {
            return;
        }
        $this->proveedorId = $proveedor->getId();
        $this->alias = $proveedor->getAlias();
        $this->nombre = $proveedor->getNombre();
        $this->telefono = $proveedor->getTelefono();
        $this->fax= $proveedor->getFax();
        $this->web= $proveedor->getWeb();
        $this->direccion= $proveedor->getDireccion();
        $this->ciudad= $proveedor->getCiudad();
        $this->region= $proveedor->getRegion();
        $this->apartado= $proveedor->getApartado();
        $this->pais = $proveedor->getPais();
        $this->puesto= $proveedor->getPuesto();
        $this->celular= $proveedor->getCelular();
        $this->email=$proveedor->getEmail();
        $this->moneda = $proveedor->getMoneda();
        $this->creditoLimite = $proveedor->getCreditoLimite();
        $this->cifNif= $proveedor->getCifNif();
        $this->pago=$proveedor->getPago();
        $this->observaciones = $proveedor->getObservaciones();
        $provMarcas = $proveedor->getMarcas();
        foreach ($provMarcas as $provMarca) {
            $this->marcas[] = $provMarca;
        }
    }

    public function getTerceroData()
    {
        $tercero = new Tercero();

        $tercero->setId($this->getTerceroId());
        $tercero->setFoto($this->getFoto());
        //$tercero->setCodigo($this->getCodigo());
        $tercero->setAlias($this->getAlias());
        $tercero->setNombres($this->getNombre());
        //$tercero->setApellidos($this->getApellidos());
        //$tercero->setActivo($this->getActivo());
        $tercero->setCifNif($this->getCifNif());
        $tercero->setEmail($this->getEmail());
        $tercero->setWeb($this->getWeb());

        return $tercero;
    }

    public function getProveedorData()
    {
        $proveedor = new Proveedor();

        $proveedor->setAlias($this->getAlias());
        $proveedor->setNombre($this->getNombre());
        $proveedor->setTelefono($this->getTelefono());
        $proveedor->setFax($this->getFax());
        $proveedor->setWeb($this->getWeb());
        $proveedor->setDireccion($this->getDireccion());
        $proveedor->setCiudad($this->getCiudad());
        $proveedor->setRegion($this->getRegion());
        $proveedor->setApartado($this->getApartado());
        $proveedor->setPais($this->getPais());
        $proveedor->setPuesto($this->getPuesto());
        $proveedor->setCelular($this->getCelular());
        $proveedor->setEmail($this->getEmail());
        $proveedor->setMoneda($this->getMoneda());
        $proveedor->setCreditoLimite($this->getCreditoLimite());
        $proveedor->setCifNif($this->getCifNif());
        $proveedor->setPago($this->getPago());
        $proveedor->setObservaciones($this->getObservaciones());

        foreach ($this->marcas as $marca) {
            $proveedor->addMarca($marca);
        }

        return $proveedor;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return string
     */
    public function getPuesto()
    {
        return $this->puesto;
    }

    /**
     * @param string $puesto
     */
    public function setPuesto($puesto)
    {
        $this->puesto = $puesto;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * @return string
     */
    public function getApartado()
    {
        return $this->apartado;
    }

    /**
     * @param string $apartado
     */
    public function setApartado($apartado)
    {
        $this->apartado = $apartado;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * @return string
     */
    public function getCifNif()
    {
        return $this->cifNif;
    }

    /**
     * @param string $cifNif
     */
    public function setCifNif($cifNif)
    {
        $this->cifNif = $cifNif;
    }

    /**
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * @param string $ciudad
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }

    /**
     * @return string
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * @param string $contacto
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * @return int
     */
    public function getCreditoLimite()
    {
        return $this->creditoLimite;
    }

    /**
     * @param int $creditoLimite
     */
    public function setCreditoLimite($creditoLimite)
    {
        $this->creditoLimite = $creditoLimite;
    }

    /**
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return UploadResources
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * @param UploadResources $foto
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    /**
     * @return array
     */
    public function getMarcas()
    {
        return $this->marcas;
    }

    /**
     * @param array $marcas
     */
    public function setMarcas($marcas)
    {
        $this->marcas = $marcas;
    }

    /**
     * @return Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * @param Moneda $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param string $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    /**
     * @return string
     */
    public function getPago()
    {
        return $this->pago;
    }

    /**
     * @param string $pago
     */
    public function setPago($pago)
    {
        $this->pago = $pago;
    }

    /**
     * @return string
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * @param string $pais
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
    }

    /**
     * @return int
     */
    public function getProveedorId()
    {
        return $this->proveedorId;
    }

    /**
     * @param int $proveedorId
     */
    public function setProveedorId($proveedorId)
    {
        $this->proveedorId = $proveedorId;
    }

    /**
     * @return int
     */
    public function getTerceroId()
    {
        return $this->TerceroId;
    }

    /**
     * @param int $TerceroId
     */
    public function setTerceroId($TerceroId)
    {
        $this->TerceroId = $TerceroId;
    }
}

<?php

namespace Buseta\BodegaBundle\Entity;

use Buseta\BodegaBundle\Form\Model\ProveedorModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proveedor.
 *
 * @ORM\Table(name="d_proveedor")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\ProveedorRepository")
 */
class Proveedor
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
     * @var \Buseta\BodegaBundle\Entity\Tercero
     *
     * @ORM\OneToOne(targetEntity="Buseta\BodegaBundle\Entity\Tercero", inversedBy="proveedor")
     */
    private $tercero;

    /**
     * @var \HatueySoft\UploadBundle\Entity\UploadResources
     *
     * @ORM\OneToOne(targetEntity="HatueySoft\UploadBundle\Entity\UploadResources", cascade={"all"})
     */
    private $foto;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string")
     * @Assert\NotBlank()
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string")
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=25, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=25, nullable=true)
     */
    private $fax;

    /**
     * @var string
     * @ORM\Column(name="web", type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * @ORM\Column(name="direccion", type="text", nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(name="ciudad", type="string", length=255, nullable=true)
     */
    private $ciudad;

    /**
     * @ORM\Column(name="region", type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(name="apartado", type="string", length=255, nullable=true)
     */
    private $apartado;

    /**
     * @ORM\Column(name="pais", type="string", length=255, nullable=true)
     */
    private $pais;

    /**
     * @ORM\Column(name="contacto", type="string", length=255, nullable=true)
     */
    private $contacto;

    /**
     * @ORM\Column(name="puesto", type="string", length=255, nullable=true)
     */
    private $puesto;

    /**
     * @ORM\Column(name="celular", type="string", length=25, nullable=true)
     */
    private $celular;

    /**
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var \Buseta\NomencladorBundle\Entity\Moneda
     *
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Moneda")
     */
    private $moneda;

    /**
     * @var string
     *
     * @ORM\Column(name="creditoLimite", type="decimal", nullable=true)
     */
    private $creditoLimite;

    /**
     * @var string
     *
     * @ORM\Column(name="cifNif", type="string", nullable=true)
     */
    private $cifNif;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="pago", type="string", length=255, nullable=true)
     */
    private $pago;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Buseta\NomencladorBundle\Entity\MarcaProveedor", inversedBy="proveedores", cascade={"persist","remove"})
     * @ORM\JoinTable(name="d_proveedor_marcas")
     */
    private $marcas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->marcas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Establece los valores desde el modelo en la entidad.
     *
     * @param ProveedorModel $model
     *
     * @return $this
     */
    public function setModelData(ProveedorModel $model)
    {
        $this->setAlias($model->getAlias());
        $this->setNombre($model->getNombre());
        $this->setTelefono($model->getTelefono());
        $this->setFax($model->getFax());
        $this->setWeb($model->getWeb());
        $this->setDireccion($model->getDireccion());
        $this->setCiudad($model->getCiudad());
        $this->setRegion($model->getRegion());
        $this->setApartado($model->getApartado());
        $this->setPais($model->getPais());
        $this->setPuesto($model->getPuesto());
        $this->setCelular($model->getCelular());
        $this->setEmail($model->getEmail());
        $this->setMoneda($model->getMoneda());
        $this->setCreditoLimite($model->getCreditoLimite());
        $this->setCifNif($model->getCifNif());
        $this->setPago($model->getPago());
        $this->setObservaciones($model->getObservaciones());

        $marcasNew = $model->getMarcas();
        foreach ($marcasNew as $marcaNew) {
            $found = false;
            foreach ($this->marcas as $marcaOld) {
                if ($marcaOld->getId() == $marcaNew->getId()) {
                    $found = true;
                }
            }
            if (!$found) {
                $this->addMarca($marcaNew);
            }
        }
        foreach ($this->marcas as $marcaOld) {
            $found = false;
            foreach ($marcasNew as $marcaNew) {
                if ($marcaOld->getId() == $marcaNew->getId()) {
                    $found = true;
                }
            }
            if (!$found) {
                $this->removeMarca($marcaOld);
            }
        }

        return $this;
    }

    /**
     * Set id.
     *
     * @param integer $id
     *
     * @return Proveedor
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set creditoLimite.
     *
     * @param string $creditoLimite
     *
     * @return Proveedor
     */
    public function setCreditoLimite($creditoLimite)
    {
        $this->creditoLimite = $creditoLimite;

        return $this;
    }

    /**
     * Get creditoLimite.
     *
     * @return string
     */
    public function getCreditoLimite()
    {
        return $this->creditoLimite;
    }

    /**
     * Set observaciones.
     *
     * @param string $observaciones
     *
     * @return Proveedor
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones.
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set moneda.
     *
     * @param \Buseta\NomencladorBundle\Entity\Moneda $moneda
     *
     * @return Proveedor
     */
    public function setMoneda(\Buseta\NomencladorBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda.
     *
     * @return \Buseta\NomencladorBundle\Entity\Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Add marcas
     *
     * @param \Buseta\NomencladorBundle\Entity\MarcaProveedor $marcas
     * @return Proveedor
     */
    public function addMarca(\Buseta\NomencladorBundle\Entity\MarcaProveedor $marca)
    {
        $marca->addProveedore($this);
        $this->marcas[] = $marca;

        return $this;
    }

    /**
     * Remove marcas
     *
     * @param \Buseta\NomencladorBundle\Entity\MarcaProveedor $marcas
     */
    public function removeMarca(\Buseta\NomencladorBundle\Entity\MarcaProveedor $marca)
    {

        $this->marcas->removeElement($marca);
    }

    /**
     * Get marcas
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMarcas()
    {
        return $this->marcas;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * @return mixed
     */
    public function getApartado()
    {
        return $this->apartado;
    }

    /**
     * @param mixed $apartado
     */
    public function setApartado($apartado)
    {
        $this->apartado = $apartado;
    }

    /**
     * @return mixed
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param mixed $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * @return mixed
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * @param mixed $ciudad
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }

    /**
     * @return mixed
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * @param mixed $contacto
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
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
     * @return mixed
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * @param mixed $pais
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
    }

    /**
     * @return mixed
     */
    public function getPuesto()
    {
        return $this->puesto;
    }

    /**
     * @param mixed $puesto
     */
    public function setPuesto($puesto)
    {
        $this->puesto = $puesto;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
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
     * @return \HatueySoft\UploadBundle\Entity\UploadResources
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * @param \HatueySoft\UploadBundle\Entity\UploadResources $foto
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;
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
     * @return Tercero
     */
    public function getTercero()
    {
        return $this->tercero;
    }

    /**
     * @param Tercero $tercero
     */
    public function setTercero($tercero)
    {
        $this->tercero = $tercero;
    }
}

<?php

namespace Buseta\CombustibleBundle\Entity;

use Buseta\BodegaBundle\Interfaces\GeneradorBitacoraInterface;
use Buseta\CombustibleBundle\ServicioCombustibleStatus;
use Doctrine\ORM\Mapping as ORM;
use HatueySoft\SecurityBundle\Interfaces\DateTimeAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ServicioCombustible.
 *
 * @ORM\Table(name="d_servicio_combustible")
 * @ORM\Entity(repositoryClass="Buseta\CombustibleBundle\Entity\Repository\ServicioCombustibleRepository")
 */
class ServicioCombustible implements GeneradorBitacoraInterface, DateTimeAwareInterface
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
     * @var \Buseta\CombustibleBundle\Entity\ConfiguracionCombustible
     *
     * @ORM\ManyToOne(targetEntity="Buseta\CombustibleBundle\Entity\ConfiguracionCombustible")
     */
    private $combustible;

    /**
     * @var integer
     *
     * @ORM\Column(name="odometro", type="integer")
     *
     */
    private $odometro;

    /**
     * The allowed values for this list are:
     * DR (Abierto)
     * PR (Revisado)
     * CO (Serviciado)
     *
     * @var string
     *
     * @ORM\Column(name="estado", type="string", nullable=false)
     * @Assert\Choice(choices={"DR", "PR", "CO"})
     *
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_libros", type="integer")
     * @Assert\NotBlank()
     */
    private $cantidadLibros;

    /**
     * @var \Buseta\BusesBundle\Entity\Chofer
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BusesBundle\Entity\Chofer")
     */
    private $chofer;

    /**
     * @var \Buseta\BusesBundle\Entity\Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BusesBundle\Entity\Vehiculo")
     */
    private $vehiculo;

    /**
     * @var string
     *
     * @ORM\Column(name="boleta", type="string", length=64, nullable=true)
     */
    private $boleta;

    /**
     * @var string
     *
     * @ORM\Column(name="marchamo_1", type="string")
     */
    private $marchamo1;

    /**
     * @var string
     *
     * @ORM\Column(name="marchamo_2", type="string")
     */
    private $marchamo2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="HatueySoft\SecurityBundle\Entity\User")
     */
    private $createdby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="HatueySoft\SecurityBundle\Entity\User")
     */
    private $updatedby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="HatueySoft\SecurityBundle\Entity\User")
     */
    private $deletedby;


    /**
     * ServicioCombustible constructor.
     */
    public function __construct()
    {
        $this->estado = ServicioCombustibleStatus::SERVICIO_COMBUSTIBLE_STATUS_PROCESS;
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
     * Set createdby
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $createdby
     *
     * @return ServicioCombustible
     */
    public function setCreatedby(\HatueySoft\SecurityBundle\Entity\User $createdby = null)
    {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get createdby
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * Set updatedby
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $updatedby
     *
     * @return ServicioCombustible
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

    /**
     * Set deletedby
     *
     * @param \HatueySoft\SecurityBundle\Entity\User $deletedby
     *
     * @return ServicioCombustible
     */
    public function setDeletedby(\HatueySoft\SecurityBundle\Entity\User $deletedby = null)
    {
        $this->deletedby = $deletedby;

        return $this;
    }

    /**
     * Get deletedby
     *
     * @return \HatueySoft\SecurityBundle\Entity\User
     */
    public function getDeletedby()
    {
        return $this->deletedby;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ServicioCombustible
     */
    public function setCreated(\DateTime $created = null)
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
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return ServicioCombustible
     */
    public function setUpdated(\DateTime $updated = null)
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
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return ServicioCombustible
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
     * Set cantidadLibros
     *
     * @param integer $cantidadLibros
     * @return ServicioCombustible
     */
    public function setCantidadLibros($cantidadLibros)
    {
        $this->cantidadLibros = $cantidadLibros;

        return $this;
    }

    /**
     * Get cantidadLibros
     *
     * @return integer
     */
    public function getCantidadLibros()
    {
        return $this->cantidadLibros;
    }

    /**
     * Set combustible
     *
     * @param \Buseta\CombustibleBundle\Entity\ConfiguracionCombustible $combustible
     * @return ServicioCombustible
     */
    public function setCombustible(\Buseta\CombustibleBundle\Entity\ConfiguracionCombustible $combustible = null)
    {
        $this->combustible = $combustible;

        return $this;
    }

    /**
     * Get combustible
     *
     * @return \Buseta\CombustibleBundle\Entity\ConfiguracionCombustible
     */
    public function getCombustible()
    {
        return $this->combustible;
    }

    /**
     * Set chofer
     *
     * @param \Buseta\BusesBundle\Entity\Chofer $chofer
     * @return ServicioCombustible
     */
    public function setChofer(\Buseta\BusesBundle\Entity\Chofer $chofer = null)
    {
        $this->chofer = $chofer;

        return $this;
    }

    /**
     * Get chofer
     *
     * @return \Buseta\BusesBundle\Entity\Chofer
     */
    public function getChofer()
    {
        return $this->chofer;
    }

    /**
     * Set vehiculo
     *
     * @param \Buseta\BusesBundle\Entity\Vehiculo $vehiculo
     * @return ServicioCombustible
     */
    public function setVehiculo(\Buseta\BusesBundle\Entity\Vehiculo $vehiculo = null)
    {
        $this->vehiculo = $vehiculo;

        return $this;
    }

    /**
     * Get vehiculo
     *
     * @return \Buseta\BusesBundle\Entity\Vehiculo
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    /**
     * Set marchamo1
     *
     * @param integer $marchamo1
     * @return ServicioCombustible
     */
    public function setMarchamo1($marchamo1)
    {
        $this->marchamo1 = $marchamo1;

        return $this;
    }

    /**
     * Get marchamo1
     *
     * @return integer
     */
    public function getMarchamo1()
    {
        return $this->marchamo1;
    }

    /**
     * Set marchamo2
     *
     * @param integer $marchamo2
     * @return ServicioCombustible
     */
    public function setMarchamo2($marchamo2)
    {
        $this->marchamo2 = $marchamo2;

        return $this;
    }

    /**
     * Get marchamo2
     *
     * @return integer
     */
    public function getMarchamo2()
    {
        return $this->marchamo2;
    }

    /**
     * @return int
     */
    public function getOdometro()
    {
        return $this->odometro;
    }

    /**
     * @param int $odometro
     */
    public function setOdometro($odometro)
    {
        $this->odometro = $odometro;
    }

    /**
     * Set boleta
     *
     * @param string $boleta
     *
     * @return ServicioCombustible
     */
    public function setBoleta($boleta)
    {
        $this->boleta = $boleta;

        return $this;
    }

    /**
     * Get boleta
     *
     * @return string
     */
    public function getBoleta()
    {
        return $this->boleta;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return ServicioCombustible
     */
    public function setFecha(\DateTime $fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}

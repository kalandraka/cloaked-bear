<?php

namespace Buseta\BusesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\BusesBundle\Form\Model\VehiculoModel;

/**
 * @ORM\Table(name="d_vehiculo")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"autobus" = "Autobus", "vehiculo" = "Vehiculo"})
 * @ORM\Entity(repositoryClass="Buseta\BusesBundle\Entity\Repository\VehiculoRepository")
 */
class Vehiculo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="matricula", type="string", length=32)
     * @Assert\NotBlank(groups={"web", "console", "Autobus"})
     */
    protected $matricula;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=32)
     */
    protected $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="marcaCajacambio", type="string", nullable=true)
     */
    protected $marcaCajacambio;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoCajacambio", type="string", nullable=true)
     */
    protected $tipoCajacambio;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Marca")
     */
    protected $marca;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Modelo")
     */
    protected $modelo;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Estilo")
     */
    protected $estilo;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Color")
     */
    protected $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="numeroPlazas", type="integer")
     * @Assert\Type("integer", groups={"web", "console", "Autobus"})
     */
    protected $numeroPlazas;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\MarcaMotor")
     */
    protected $marcaMotor;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\Combustible")
     */
    protected $combustible;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     * @Assert\Type("integer", groups={"web", "console", "Autobus"})
     */
    protected $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="capacidadTanque", type="integer")
     * @Assert\Type("integer", groups={"web", "console", "Autobus"})
     */
    protected $capacidadTanque;

    /**
     * @var integer
     *
     * @ORM\Column(name="potencia", type="integer")
     * @Assert\Type("integer", groups={"web", "console", "BaseVehiculo"})
     */
    protected $potencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="numeroCilindros", type="integer")
     * @Assert\Type("integer", groups={"web", "console", "BaseVehiculo"})
     */
    protected $numeroCilindros;

    /**
     * @var integer
     *
     * @ORM\Column(name="cilindrada", type="integer")
     * @Assert\Type("integer", groups={"web", "console", "BaseVehiculo"})
     */
    protected $cilindrada;

    /**
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    protected $activo;

    /**
     * @var integer
     *
     * @ORM\Column(name="kilometraje", type="integer", nullable=true)
     */
    protected $kilometraje;

    /**
     * @var integer
     *
     * @ORM\Column(name="horas", type="integer", nullable=true)
     */
    protected $horas;

    /**
     * @param VehiculoModel $model
     * @return Vehiculo
     */
    public function setModelData(VehiculoModel $model)
    {
        $this->matricula = $model->getMatricula();
        $this->numero = $model->getNumero();
        $this->numeroCilindros = $model->getNumeroCilindros();
        $this->numeroPlazas = $model->getNumeroPlazas();
        $this->cilindrada = $model->getCilindrada();
        $this->potencia = $model->getPotencia();
        $this->anno = $model->getAnno();
        $this->capacidadTanque = $model->getCapacidadTanque();
        $this->activo = $model->isActivo();
        $this->kilometraje = $model->getKilometraje();
        $this->marcaCajacambio = $model->getMarcaCajacambio();
        $this->tipoCajacambio = $model->getTipoCajacambio();
        $this->horas = $model->getHoras();

        if ($model->getMarca()) {
            $this->marca  = $model->getMarca();
        }
        if ($model->getMarcaMotor()) {
            $this->marcaMotor  = $model->getMarcaMotor();
        }
        if ($model->getModelo()) {
            $this->modelo  = $model->getModelo();
        }
        if ($model->getEstilo()) {
            $this->estilo  = $model->getEstilo();
        }
        if ($model->getColor()) {
            $this->color  = $model->getColor();
        }
        if ($model->getCombustible()) {
            $this->combustible  = $model->getCombustible();
        }

        return $this;
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
     * Set matricula
     *
     * @param string $matricula
     * @return Vehiculo
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula
     *
     * @return string
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Vehiculo
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set marcaCajacambio
     *
     * @param string $marcaCajacambio
     * @return Vehiculo
     */
    public function setMarcaCajacambio($marcaCajacambio)
    {
        $this->marcaCajacambio = $marcaCajacambio;

        return $this;
    }

    /**
     * Get marcaCajacambio
     *
     * @return string
     */
    public function getMarcaCajacambio()
    {
        return $this->marcaCajacambio;
    }

    /**
     * Set tipoCajacambio
     *
     * @param string $tipoCajacambio
     * @return Vehiculo
     */
    public function setTipoCajacambio($tipoCajacambio)
    {
        $this->tipoCajacambio = $tipoCajacambio;

        return $this;
    }

    /**
     * Get tipoCajacambio
     *
     * @return string
     */
    public function getTipoCajacambio()
    {
        return $this->tipoCajacambio;
    }

    /**
     * Set numeroPlazas
     *
     * @param integer $numeroPlazas
     * @return Vehiculo
     */
    public function setNumeroPlazas($numeroPlazas)
    {
        $this->numeroPlazas = $numeroPlazas;

        return $this;
    }

    /**
     * Get numeroPlazas
     *
     * @return integer
     */
    public function getNumeroPlazas()
    {
        return $this->numeroPlazas;
    }

    /**
     * Set anno
     *
     * @param integer $anno
     * @return Vehiculo
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set capacidadTanque
     *
     * @param integer $capacidadTanque
     * @return Vehiculo
     */
    public function setCapacidadTanque($capacidadTanque)
    {
        $this->capacidadTanque = $capacidadTanque;

        return $this;
    }

    /**
     * Get capacidadTanque
     *
     * @return integer
     */
    public function getCapacidadTanque()
    {
        return $this->capacidadTanque;
    }

    /**
     * Set potencia
     *
     * @param integer $potencia
     * @return Vehiculo
     */
    public function setPotencia($potencia)
    {
        $this->potencia = $potencia;

        return $this;
    }

    /**
     * Get potencia
     *
     * @return integer
     */
    public function getPotencia()
    {
        return $this->potencia;
    }

    /**
     * Set numeroCilindros
     *
     * @param integer $numeroCilindros
     * @return Vehiculo
     */
    public function setNumeroCilindros($numeroCilindros)
    {
        $this->numeroCilindros = $numeroCilindros;

        return $this;
    }

    /**
     * Get numeroCilindros
     *
     * @return integer
     */
    public function getNumeroCilindros()
    {
        return $this->numeroCilindros;
    }

    /**
     * Set cilindrada
     *
     * @param integer $cilindrada
     * @return Vehiculo
     */
    public function setCilindrada($cilindrada)
    {
        $this->cilindrada = $cilindrada;

        return $this;
    }

    /**
     * Get cilindrada
     *
     * @return integer
     */
    public function getCilindrada()
    {
        return $this->cilindrada;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Vehiculo
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
     * Set kilometraje
     *
     * @param integer $kilometraje
     * @return Vehiculo
     */
    public function setKilometraje($kilometraje)
    {
        $this->kilometraje = $kilometraje;

        return $this;
    }

    /**
     * Get kilometraje
     *
     * @return integer
     */
    public function getKilometraje()
    {
        return $this->kilometraje;
    }

    /**
     * Set horas
     *
     * @param integer $horas
     * @return Vehiculo
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;

        return $this;
    }

    /**
     * Get horas
     *
     * @return integer
     */
    public function getHoras()
    {
        return $this->horas;
    }

    /**
     * Set marca
     *
     * @param \Buseta\NomencladorBundle\Entity\Marca $marca
     * @return Vehiculo
     */
    public function setMarca(\Buseta\NomencladorBundle\Entity\Marca $marca = null)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return \Buseta\NomencladorBundle\Entity\Marca
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set modelo
     *
     * @param \Buseta\NomencladorBundle\Entity\Modelo $modelo
     * @return Vehiculo
     */
    public function setModelo(\Buseta\NomencladorBundle\Entity\Modelo $modelo = null)
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get modelo
     *
     * @return \Buseta\NomencladorBundle\Entity\Modelo
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set estilo
     *
     * @param \Buseta\NomencladorBundle\Entity\Estilo $estilo
     * @return Vehiculo
     */
    public function setEstilo(\Buseta\NomencladorBundle\Entity\Estilo $estilo = null)
    {
        $this->estilo = $estilo;

        return $this;
    }

    /**
     * Get estilo
     *
     * @return \Buseta\NomencladorBundle\Entity\Estilo
     */
    public function getEstilo()
    {
        return $this->estilo;
    }

    /**
     * Set color
     *
     * @param \Buseta\NomencladorBundle\Entity\Color $color
     * @return Vehiculo
     */
    public function setColor(\Buseta\NomencladorBundle\Entity\Color $color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return \Buseta\NomencladorBundle\Entity\Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set marcaMotor
     *
     * @param \Buseta\NomencladorBundle\Entity\MarcaMotor $marcaMotor
     * @return Vehiculo
     */
    public function setMarcaMotor(\Buseta\NomencladorBundle\Entity\MarcaMotor $marcaMotor = null)
    {
        $this->marcaMotor = $marcaMotor;

        return $this;
    }

    /**
     * Get marcaMotor
     *
     * @return \Buseta\NomencladorBundle\Entity\MarcaMotor
     */
    public function getMarcaMotor()
    {
        return $this->marcaMotor;
    }

    /**
     * Set combustible
     *
     * @param \Buseta\NomencladorBundle\Entity\Combustible $combustible
     * @return Vehiculo
     */
    public function setCombustible(\Buseta\NomencladorBundle\Entity\Combustible $combustible = null)
    {
        $this->combustible = $combustible;

        return $this;
    }

    /**
     * Get combustible
     *
     * @return \Buseta\NomencladorBundle\Entity\Combustible
     */
    public function getCombustible()
    {
        return $this->combustible;
    }

    public function __toString()
    {
        return sprintf('%d (%s)', $this->getNumero(), $this->getMatricula());
    }
}

<?php

namespace Buseta\TallerBundle\Form\Model;

use Buseta\TallerBundle\Entity\Diagnostico;
use Buseta\TallerBundle\Entity\TareaAdicional;
use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrdenTrabajo Model
 *
 */
class OrdenTrabajoModel
{
    /**
     * @var integer
     *
     */
    private $id;

    /**
     * @var string
     */
    private $numero;

    /**
     * @var \Buseta\TallerBundle\Entity\Diagnostico
     * @Assert\NotBlank()
     */
    private $diagnostico;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     */
    private $realizadaPor;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     * @Assert\NotNull()
     */
    private $diagnosticadoPor;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     */
    private $ayudante;

    /**
     * @var \DateTime
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     */
    private $fechaFinal;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tareasAdicionales;

    /**
     * @var string
     */
    private $duracionDias;

    /**
     * @var string
     */
    private $duracionHorasLaboradas;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     */
    private $revisadoPor;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     */
    private $aprobadoPor;

    /**
     * @var integer
     */
    private $kilometraje;

    /**
     * @var \Buseta\BusesBundle\Entity\Autobus
     * @Assert\NotBlank()
     */
    private $autobus;

    /**
     * @var \Buseta\TallerBundle\Entity\Diagnostico
     */
    private $prioridad;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $estado = 'BO';

    /**
     * @var string
     */
    private $observaciones;

    /**
     * @var string
     */
    private $requisionMateriales;

    /**
     * @var boolean
     */
    private $cancelado;

    /**
     * @var \DateTime
     *
     */
    private $created;

    /**
     * @var \DateTime
     *
     */
    private $updated;

    /**
     * @var boolean
     *
     */
    private $deleted;

    /**
     * Constructor
     */
    public function __construct(OrdenTrabajo $ordenTrabajo = null)
    {
        $this->tareaDiagnostico = new \Doctrine\Common\Collections\ArrayCollection();

        if ($ordenTrabajo !== null) {
            $this->id = $ordenTrabajo->getId();
            $this->created = $ordenTrabajo->getCreated();
            $this->deleted = $ordenTrabajo->getDeleted();
            $this->updated = $ordenTrabajo->getUpdated();
            $this->estado = $ordenTrabajo->getEstado();
            $this->numero = $ordenTrabajo->getNumero();
            $this->observaciones = $ordenTrabajo->getObservaciones();
            $this->cancelado = $ordenTrabajo->getCancelado();
            $this->fechaInicio = $ordenTrabajo->getFechaInicio();
            $this->fechaFinal =  $ordenTrabajo->getFechaFinal();
            $this->duracionDias =  $ordenTrabajo->getDuracionDias();
            $this->duracionHorasLaboradas =  $ordenTrabajo->getDuracionHorasLaboradas();
            $this->kilometraje =  $ordenTrabajo->getKilometraje();
            $this->realizadaPor =  $ordenTrabajo->getRealizadaPor();
            $this->ayudante =  $ordenTrabajo->getAyudante();
            $this->revisadoPor =  $ordenTrabajo->getRevisadoPor();
            $this->realizadaPor =  $ordenTrabajo->getRealizadaPor();
            $this->aprobadoPor =  $ordenTrabajo->getAprobadoPor();
            $this->requisionMateriales =  $ordenTrabajo->getRequisionMateriales();

            if ($ordenTrabajo->getDiagnostico()) {
                $this->diagnostico  = $ordenTrabajo->getDiagnostico();
            }
            if ($ordenTrabajo->getDiagnosticadoPor()) {
                $this->diagnosticadoPor  = $ordenTrabajo->getDiagnosticadoPor();
            }
            $this->tareasAdicionales = new ArrayCollection();
            if ($ordenTrabajo->getTareasAdicionales() !== null && $ordenTrabajo->getTareasAdicionales()->count() > 0) {
                foreach ($ordenTrabajo->getTareasAdicionales() as $tareasAdicionale) {
                    $this->tareasAdicionales->add($tareasAdicionale);
                }
            }
            if ($ordenTrabajo->getAutobus()) {
                $this->autobus  = $ordenTrabajo->getAutobus();
            }
            if ($ordenTrabajo->getPrioridad()) {
                $this->prioridad  = $ordenTrabajo->getPrioridad();
            }
        }
    }

    /**
     * @return  OrdenTrabajo
     */
    public function getEntityData()
    {

        $ordenTrabajo = new OrdenTrabajo();
        $ordenTrabajo->setCreated ($this->getCreated());
        $ordenTrabajo->setDeleted ($this->getDeleted());
        $ordenTrabajo->setUpdated ($this->getUpdated());
        $ordenTrabajo->setEstado($this->getEstado());
        $ordenTrabajo->setNumero($this->getNumero());
        $ordenTrabajo->setObservaciones($this->getObservaciones());
        $ordenTrabajo->setCancelado($this->getCancelado());
        $ordenTrabajo->setFechaInicio($this->getFechaInicio());
        $ordenTrabajo->setFechaFinal($this->getFechaFinal());
        $ordenTrabajo->setDuracionDias($this->getDuracionDias());
        $ordenTrabajo->setDuracionHorasLaboradas($this->getDuracionHorasLaboradas());
        $ordenTrabajo->setKilometraje($this->getKilometraje());
        $ordenTrabajo->setDiagnosticadoPor($this->getDiagnosticadoPor());
        $ordenTrabajo->setAyudante($this->getAyudante());
        $ordenTrabajo->setRevisadoPor($this->getRevisadoPor());
        $ordenTrabajo->setRealizadaPor($this->getRealizadaPor());
        $ordenTrabajo->setAprobadoPor($this->getAprobadoPor());
        $ordenTrabajo->setRequisionMateriales($this->getRequisionMateriales());

        if ($this->getDiagnostico() !== null) {
            $ordenTrabajo->setDiagnostico($this->getDiagnostico());
        }
        if ($this->getAutobus() !== null) {
            $ordenTrabajo->setAutobus($this->getAutobus());
        }
        if ($this->getPrioridad() !== null) {
            $ordenTrabajo->setPrioridad($this->getPrioridad());
        }

        if (!$this->getTareasAdicionales()->isEmpty()) {
            foreach ($this->getTareasAdicionales() as $tareasAdicionale) {
                $ordenTrabajo->addTareasAdicionale($tareasAdicionale);
            }
        }


        return $ordenTrabajo;
    }

    /**
     * @return ArrayCollection
     */
    public function getTareasAdicionales()
    {
        return $this->tareaDiagnostico;
    }

    /**
     * @param ArrayCollection $tareaAdicionales
     */
    public function setTareasAdicionales($tareaAdicionales)
    {
        $this->tareaAdicionales = $tareaAdicionales;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return \Buseta\TallerBundle\Entity\OrdenTrabajo
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @return string
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     * @param string $diagnostico
     */
    public function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;
    }

    /**
     * @return string
     */
    public function getAutobus()
    {
        return $this->autobus;
    }

    /**
     * @param string $autobus
     */
    public function setAutobus($autobus)
    {
        $this->autobus = $autobus;
    }

    /**
     * @return string
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * @param string $prioridad
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;
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
    public function getRequisionMateriales()
    {
        return $this->requisionMateriales;
    }

    /**
     * @param string $requisionMateriales
     */
    public function setRequisionMateriales($requisionMateriales)
    {
        $this->requisionMateriales = $requisionMateriales;
    }

    /**
     * @return string
     */
    public function getCancelado()
    {
        return $this->cancelado;
    }

    /**
     * @param string $cancelado
     */
    public function setCancelado($cancelado)
    {
        $this->cancelado = $cancelado;
    }

    /**
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param string $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return \DateTime
     */
    public function getFechaFinal()
    {
        return $this->fechaFinal;
    }

    /**
     * @param string $fechaFinal
     */
    public function setFechaFinal($fechaFinal)
    {
        $this->fechaFinal = $fechaFinal;
    }

    /**
     * @return \DateTime
     */
    public function getDuracionHorasLaboradas()
    {
        return $this->duracionHorasLaboradas;
    }

    /**
     * @param string $duracionHorasLaboradas
     */
    public function setDuracionHorasLaboradas($duracionHorasLaboradas)
    {
        $this->duracionHorasLaboradas = $duracionHorasLaboradas;
    }

    /**
     * @return integer
     */
    public function getKilometraje()
    {
        return $this->kilometraje;
    }

    /**
     * @param integer $kilometraje
     */
    public function setKilometraje($kilometraje)
    {
        $this->kilometraje = $kilometraje;
    }

    /**
     * @return string
     */
    public function getDiagnosticadoPor()
    {
        return $this->diagnosticadoPor;
    }

    /**
     * @param string $diagnosticadoPor
     */
    public function setDiagnosticadoPor($diagnosticadoPor)
    {
        $this->diagnosticadoPor = $diagnosticadoPor;
    }

    /**
     * @return string
     */
    public function getAyudante()
    {
        return $this->ayudante;
    }

    /**
     * @param string $ayudante
     */
    public function setAyudante($ayudante)
    {
        $this->ayudante = $ayudante;
    }


    /**
     * @return string
     */
    public function getRevisadoPor()
    {
        return $this->revisadoPor;
    }

    /**
     * @param string $revisadoPor
     */
    public function setRevisadoPor($revisadoPor)
    {
        $this->revisadoPor = $revisadoPor;
    }

    /**
     * @return string
     */
    public function getRealizadaPor()
    {
        return $this->realizadaPor;
    }

    /**
     * @param string $realizadaPor
     */
    public function setRealizadaPor($realizadaPor)
    {
        $this->realizadaPor = $realizadaPor;
    }

    /**
     * @return string
     */
    public function getAprobadoPor()
    {
        return $this->aprobadoPor;
    }

    /**
     * @param string $aprobadoPor
     */
    public function setAprobadoPor($aprobadoPor)
    {
        $this->aprobadoPor = $aprobadoPor;
    }

    /**
     * @return string
     */
    public function getDuracionDias()
    {
        return $this->duracionDias;
    }

    /**
     * @param string $duracionDias
     */
    public function setDuracionDias($duracionDias)
    {
        $this->duracionDias = $duracionDias;
    }
    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

   /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }
    /**
     * @param TareaAdicional $tareasAdicionales
     */
    public function addTareasAdicionales(TareaAdicional $tareasAdicionales)
    {
        $this->tareasAdicionales->add($tareasAdicionales);
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return sprintf('%d', $this->numero ) ;
    }
}

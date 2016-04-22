<?php

namespace Buseta\TallerBundle\Form\Model;

use Buseta\TallerBundle\Entity\Diagnostico;
use Buseta\TallerBundle\Entity\TareaAdicional;
use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Buseta\TallerBundle\Entity\TareaDiagnostico;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Diagnostico Model
 *
 */
class DiagnosticoModel
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
     * @var \Buseta\TallerBundle\Entity\Reporte
     * @Assert\NotBlank()
     */
    private $reporte;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tareaDiagnostico;

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
     * @var \Buseta\TallerBundle\Entity\ObservacionDiagnostico
     */
    private $observaciones;

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
    public function __construct(Diagnostico $diagnostico = null)
    {
        $this->tareaDiagnostico = new \Doctrine\Common\Collections\ArrayCollection();

        if ($diagnostico !== null) {
            $this->id = $diagnostico->getId();
            $this->created = $diagnostico->getCreated();
            $this->deleted = $diagnostico->getDeleted();
            $this->updated = $diagnostico->getUpdated();
            $this->estado = $diagnostico->getEstado();
            $this->numero = $diagnostico->getNumero();
            $this->observaciones = $diagnostico->getObservaciones();

            if ($diagnostico->getReporte()) {
                $this->reporte  = $diagnostico->getReporte();
            }
            if ($diagnostico->getAutobus()) {
                $this->autobus  = $diagnostico->getAutobus();
            }
            if ($diagnostico->getPrioridad()) {
                $this->prioridad  = $diagnostico->getPrioridad();
            }
            $this->tareaDiagnostico = new ArrayCollection();
            if ($diagnostico->getTareaDiagnostico() !== null && $diagnostico->getTareaDiagnostico()->count() > 0) {
                foreach ($diagnostico->getTareaDiagnostico() as $tareaDiagnostico) {
                    $this->tareaDiagnostico->add($tareaDiagnostico);
                }
            }

        }
    }

    /**
     * @return  Diagnostico
     */
    public function getEntityData()
    {

        $diagnostico = new Diagnostico();
        $diagnostico->setCreated ($this->getCreated());
        $diagnostico->setDeleted ($this->getDeleted());
        $diagnostico->setUpdated ($this->getUpdated());
        $diagnostico->setEstado($this->getEstado());
        $diagnostico->setNumero($this->getNumero());
        //$diagnostico->setObservaciones($this->getObservaciones());
        //$diagnostico->setObservaciones($this->getObservaciones());


        if ($this->getReporte() !== null) {
            $diagnostico->setReporte($this->getReporte());
        }
        if ($this->getAutobus() !== null) {
            $diagnostico->setAutobus($this->getAutobus());
        }
        if ($this->getPrioridad() !== null) {
            $diagnostico->setPrioridad($this->getPrioridad());
        }

        if (!$this->getTareaDiagnostico()->isEmpty()) {
            foreach ($this->getTareaDiagnostico() as $tareaDiagnostico) {
                $diagnostico->addTareaDiagnostico($tareaDiagnostico);
            }
        }


        return $diagnostico;
    }

    /**
     * @return ArrayCollection
     */
    public function getTareaDiagnostico()
    {
        return $this->tareaDiagnostico;
    }

    /**
     * @param ArrayCollection $tareaDiagnostico
     */
    public function setTareaDiagnostico($tareaDiagnostico)
    {
        $this->tareaDiagnostico = $tareaDiagnostico;
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
    public function getReporte()
    {
        return $this->reporte;
    }

    /**
     * @param string $reporte
     */
    public function setReporte($reporte)
    {
        $this->reporte = $reporte;
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
     * @param TareaDiagnostico $tareaDiagnostico
     */
    public function addTareaDiagnostico(TareaDiagnostico $tareaDiagnostico)
    {
        $this->tareaDiagnostico->add($tareaDiagnostico);
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return sprintf('%d', $this->numero ) ;
    }
}

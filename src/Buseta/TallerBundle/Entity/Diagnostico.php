<?php

namespace Buseta\TallerBundle\Entity;

use Buseta\TallerBundle\Form\Model\DiagnosticoModel;
use Doctrine\Common\Collections\ArrayCollection;
use HatueySoft\SecurityBundle\Doctrine\DateTimeAwareTrait;
use HatueySoft\SecurityBundle\Interfaces\DateTimeAwareInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Diagnostico
 *
 * @ORM\Table(name="d_diagnostico")
 * @ORM\Entity(repositoryClass="Buseta\TallerBundle\Entity\Repository\DiagnosticoRepository")
 */
class Diagnostico implements DateTimeAwareInterface
{
    use DateTimeAwareTrait;

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
     * @ORM\Column(name="numero", type="string")
     * @Assert\NotBlank()
     */
    private $numero;

    /**
     * @var \Buseta\TallerBundle\Entity\OrdenTrabajo
     *
     * @ORM\OneToOne(targetEntity="Buseta\TallerBundle\Entity\OrdenTrabajo", mappedBy="diagnostico")
     */
    private $ordenTrabajo;

    /**
     * @var \Buseta\TallerBundle\Entity\Reporte
     *
     * @ORM\OneToOne(targetEntity="Buseta\TallerBundle\Entity\Reporte", inversedBy="diagnostico")
     */
    private $reporte;

    /**
     * @var \Buseta\BusesBundle\Entity\Autobus
     *
     * @ORM\ManyToOne(targetEntity="Buseta\BusesBundle\Entity\Autobus")
     * @Assert\NotNull
     */
    private $autobus;

    /**
     * @var \Buseta\NomencladorBundle\Entity\PrioridadSolicitud
     *
     * @ORM\ManyToOne(targetEntity="Buseta\NomencladorBundle\Entity\PrioridadSolicitud")
     */
    private $prioridad;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Buseta\TallerBundle\Entity\ObservacionDiagnostico", mappedBy="diagnostico", cascade={"all"})
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string")
     * @Assert\NotBlank()
     */
    private $estado = 'BO';

    /**
     * @var boolean
     * @ORM\Column(name="cancelado", type="boolean")
     */
    private $cancelado;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Buseta\TallerBundle\Entity\TareaDiagnostico", mappedBy="diagnostico", cascade={"persist", "remove"})
     */
    private $tareaDiagnostico;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->observaciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tareaDiagnostico = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cancelado = false;
    }

    /**
     * @param DiagnosticoModel $model
     *
     * @deprecated Will be removed
     */
    public function legacyUpdateEntityData(DiagnosticoModel $model)
    {
        @trigger_error(sprintf('Method %s will be removed. Use DiagnosticoConverter::getEntity() instead.', __METHOD__), E_DEPRECATED);

        $this->numero = $model->getNumero();
        $this->autobus = $model->getAutobus();
        $this->prioridad = $model->getPrioridad();
        $this->estado = $model->getEstado();
        if ($model->getObservaciones()->count() > 0) {
            $this->observaciones = new ArrayCollection();
            foreach ($model->getObservaciones()->getIterator() as $obs) {
                $this->addObservacione($obs);
            }
        }
        if ($model->getTareaDiagnostico()->count() > 0) {
            $this->tareaDiagnostico = new ArrayCollection();
            foreach ($model->getTareaDiagnostico()->getIterator() as $tarea) {
                $this->addTareaDiagnostico($tarea);
            }
        }
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
     * Set autobus
     *
     * @param \Buseta\BusesBundle\Entity\Autobus $autobus
     * @return Diagnostico
     */
    public function setAutobus(\Buseta\BusesBundle\Entity\Autobus $autobus = null)
    {
        $this->autobus = $autobus;

        return $this;
    }

    /**
     * Get autobus
     *
     * @return \Buseta\BusesBundle\Entity\Autobus
     */
    public function getAutobus()
    {
        return $this->autobus;
    }

    /**
     * Add observaciones
     *
     * @param \Buseta\TallerBundle\Entity\ObservacionDiagnostico $observaciones
     * @return Diagnostico
     */
    public function addObservacione(\Buseta\TallerBundle\Entity\ObservacionDiagnostico $observaciones)
    {
        $observaciones->setDiagnostico($this);

        $this->observaciones[] = $observaciones;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Remove observaciones
     *
     * @param \Buseta\TallerBundle\Entity\ObservacionDiagnostico $observaciones
     */
    public function removeObservacione(\Buseta\TallerBundle\Entity\ObservacionDiagnostico $observaciones)
    {
        $this->observaciones->removeElement($observaciones);
    }

    /**
     * Get observaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    public function __toString()
    {
        return $this->numero;
    }

    /**
     * Set reporte
     *
     * @param \Buseta\TallerBundle\Entity\Reporte $reporte
     * @return Diagnostico
     */
    public function setReporte(\Buseta\TallerBundle\Entity\Reporte $reporte = null)
    {
        $this->reporte = $reporte;

        return $this;
    }

    /**
     * Get reporte
     *
     * @return \Buseta\TallerBundle\Entity\Reporte
     */
    public function getReporte()
    {
        return $this->reporte;
    }


    /**
     * Set ordenTrabajo
     *
     * @param \Buseta\TallerBundle\Entity\OrdenTrabajo $ordenTrabajo
     * @return Diagnostico
     */
    public function setOrdenTrabajo(\Buseta\TallerBundle\Entity\OrdenTrabajo $ordenTrabajo = null)
    {
        $this->ordenTrabajo = $ordenTrabajo;

        return $this;
    }

    /**
     * Get ordenTrabajo
     *
     * @return \Buseta\TallerBundle\Entity\OrdenTrabajo
     */
    public function getOrdenTrabajo()
    {
        return $this->ordenTrabajo;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return Diagnostico
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set prioridad
     *
     * @param \Buseta\NomencladorBundle\Entity\PrioridadSolicitud $prioridad
     * @return Reporte
     */
    public function setPrioridad(\Buseta\NomencladorBundle\Entity\PrioridadSolicitud $prioridad = null)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return \Buseta\NomencladorBundle\Entity\PrioridadSolicitud
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Add tareaDiagnostico
     *
     * @param \Buseta\TallerBundle\Entity\TareaDiagnostico $tareaDiagnostico
     * @return Diagnostico
     */
    public function addTareaDiagnostico(\Buseta\TallerBundle\Entity\TareaDiagnostico $tareaDiagnostico)
    {
        $tareaDiagnostico->setDiagnostico($this);

        $this->tareaDiagnostico[] = $tareaDiagnostico;

        return $this;
    }

    /**
     * Remove tareaDiagnostico
     *
     * @param \Buseta\TallerBundle\Entity\TareaDiagnostico $tareaDiagnostico
     */
    public function removeTareaDiagnostico(\Buseta\TallerBundle\Entity\TareaDiagnostico $tareaDiagnostico)
    {
        $tareaDiagnostico->setDiagnostico(null);

        $this->tareaDiagnostico->removeElement($tareaDiagnostico);
    }

    /**
     * Get tareaDiagnostico
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTareaDiagnostico()
    {
        return $this->tareaDiagnostico;
    }

    /**
     * Get cancelado.
     *
     * @return boolean
     *
     */
    public function getCancelado()
    {
        return $this->cancelado;
    }

    /**
     * Set cancelado
     *
     * @param boolean $cancelado
     * @return Diagnostico
     *
     */
    public function setCancelado($cancelado)
    {
        $this->cancelado = $cancelado;

        return $this;
    }


}

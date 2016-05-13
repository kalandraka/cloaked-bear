<?php

namespace Buseta\BodegaBundle\Form\Model;

use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\Interfaces\AlbaranInterface;
use Buseta\BodegaBundle\Form\Model\Converters\AlbaranConverter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

//!TODO: Eliminar propiedad estadoDocumento
//!TODO: Eliminar propiedad pedidoCompra
//!TODO: Eliminar propiedad albaranLineas
/**
 * Albaran Model
 *
 */
class AlbaranModel implements AlbaranInterface
{
    /**
     * @var integer
     *
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $numeroReferencia;

    /**
     * @var string
     */
    private $numeroDocumento;

    /**
     * @var \Buseta\BodegaBundle\Entity\Tercero
     * @Assert\NotBlank()
     */
    private $tercero;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $fechaMovimiento;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $fechaContable;

    /**
     * @Assert\NotBlank()
     * @var \Buseta\BodegaBundle\Entity\Bodega
     *
     * @deprecated Will be removed
     */
    private $almacen;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @deprecated Will be removed
     */
    private $estadoDocumento = 'BO';

    /**
     * @var \Buseta\BodegaBundle\Entity\PedidoCompra
     *
     * @deprecated Will be removed
     */
    private $pedidoCompra;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @deprecated Will be removed
     */
    private $albaranLineas;

    /**
     * @var \DateTime
     *
     * @deprecated Will be removed
     */
    private $created;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @deprecated Will be removed
     */
    private $createdby;

    /**
     * @var \DateTime
     *
     * @deprecated Will be removed
     */
    private $updated;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @deprecated Will be removed
     */
    private $updatedby;

    /**
     * @var boolean
     *
     * @deprecated Will be removed
     */
    private $deleted;

    /**
     * @var \HatueySoft\SecurityBundle\Entity\User
     *
     * @deprecated Will be removed
     */
    private $deletedby;

    /**
     * Constructor
     */
    public function __construct(Albaran $albaran = null)
    {
        $this->albaranLineas = new \Doctrine\Common\Collections\ArrayCollection();

        if ($albaran !== null) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            $albaranModel = AlbaranConverter::getModel($albaran);
            $properties = get_class_vars(__CLASS__);
            foreach ($properties as $property => $value) {
                $propertyAccessor->setValue(
                    $this,
                    $property,
                    $propertyAccessor->getValue($albaranModel, $property)
                );
            }
        }
    }

    /**
     * @return Albaran
     *
     * @deprecated Will be removed
     */
    public function getEntityData()
    {
        $albaran = new Albaran();
/*        $albaran->setCreated($this->getCreated());
        $albaran->setCreatedby($this->getCreatedby());
        $albaran->setDeleted($this->getDeleted());
        $albaran->setDeletedby($this->getDeletedby());
        $albaran->setUpdated($this->getUpdated());
        $albaran->setUpdatedby($this->getUpdatedby());*/

        $albaran->setNumeroDocumento($this->getNumeroDocumento());
        $albaran->setNumeroReferencia($this->getNumeroReferencia());
        $albaran->setFechaMovimiento($this->getFechaMovimiento());
        $albaran->setFechaContable($this->getFechaContable());
        $albaran->setEstadoDocumento($this->getEstadoDocumento());

        if ($this->getTercero() !== null) {
            $albaran->setTercero($this->getTercero());
        }
        if ($this->getAlmacen() !== null) {
            $albaran->setAlmacen($this->getAlmacen());
        }
        if ($this->getPedidoCompra() !== null) {
            $albaran->setPedidoCompra($this->getPedidoCompra());
        }
        if (!$this->getAlbaranLineas()->isEmpty()) {
            foreach ($this->getAlbaranLineas() as $lineas) {
                $albaran->addAlbaranLinea($lineas);
            }
        }

        return $albaran;
    }

    /**
     * @return ArrayCollection
     *
     * @deprecated Will be removed
     */
    public function getAlbaranLineas()
    {
        return $this->albaranLineas;
    }

    /**
     * @param ArrayCollection $albaranLinea
     *
     * @deprecated Will be removed
     */
    public function setAlbaranLineas($albaranLinea)
    {
        $this->albaranLineas = $albaranLinea;
    }

    /**
     * @return \Buseta\BodegaBundle\Entity\Bodega
     *
     * @deprecated Will be removed
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * @param \Buseta\BodegaBundle\Entity\Bodega $almacen
     *
     * @deprecated Will be removed
     */
    public function setAlmacen($almacen)
    {
        $this->almacen = $almacen;
    }

    /**
     * @param \Buseta\BodegaBundle\Entity\Bodega $bodega
     *
     * @return AlbaranModel
     */
    public function setBodega(\Buseta\BodegaBundle\Entity\Bodega $bodega)
    {
        $this->almacen = $bodega;

        return $this;
    }

    /**
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getBodega()
    {
        return $this->almacen;
    }

    /**
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * @param string $NumeroDocumento
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;
    }

    /**
     * @return \DateTime
     *
     * @deprecated Will be removed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @deprecated Will be removed
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \HatueySoft\SecurityBundle\Entity\User
     *
     * @deprecated Will be removed
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * @param \HatueySoft\SecurityBundle\Entity\User $createdby
     *
     * @deprecated Will be removed
     */
    public function setCreatedby($createdby)
    {
        $this->createdby = $createdby;
    }

    /**
     * @return boolean
     *
     * @deprecated Will be removed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @deprecated Will be removed
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return \HatueySoft\SecurityBundle\Entity\User
     *
     * @deprecated Will be removed
     */
    public function getDeletedby()
    {
        return $this->deletedby;
    }

    /**
     * @param \HatueySoft\SecurityBundle\Entity\User $deletedby
     *
     * @deprecated Will be removed
     */
    public function setDeletedby($deletedby)
    {
        $this->deletedby = $deletedby;
    }

    /**
     * @return string
     *
     * @deprecated Will be removed
     */
    public function getEstadoDocumento()
    {
        return $this->estadoDocumento;
    }

    /**
     * @param string $estadoDocumento
     *
     * @deprecated Will be removed
     */
    public function setEstadoDocumento($estadoDocumento)
    {
        $this->estadoDocumento = $estadoDocumento;
    }

    /**
     * @return \DateTime
     */
    public function getFechaContable()
    {
        return $this->fechaContable;
    }

    /**
     * @param \DateTime $fechaContable
     */
    public function setFechaContable($fechaContable)
    {
        $this->fechaContable = $fechaContable;
    }

    /**
     * @return \DateTime
     */
    public function getFechaMovimiento()
    {
        return $this->fechaMovimiento;
    }

    /**
     * @param \DateTime $fechaMovimiento
     */
    public function setFechaMovimiento($fechaMovimiento)
    {
        $this->fechaMovimiento = $fechaMovimiento;
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
     * @return string
     */
    public function getNumeroReferencia()
    {
        return $this->numeroReferencia;
    }

    /**
     * @param string $numeroReferencia
     */
    public function setNumeroReferencia($numeroReferencia)
    {
        $this->numeroReferencia = $numeroReferencia;
    }

    /**
     * @return \Buseta\BodegaBundle\Entity\PedidoCompra
     *
     * @deprecated Will be removed
     */
    public function getPedidoCompra()
    {
        return $this->pedidoCompra;
    }

    /**
     * @param \Buseta\BodegaBundle\Entity\PedidoCompra $pedidoCompra
     *
     * @deprecated Will be removed
     */
    public function setPedidoCompra($pedidoCompra)
    {
        $this->pedidoCompra = $pedidoCompra;
    }

    /**
     * @return \Buseta\BodegaBundle\Entity\Tercero
     */
    public function getTercero()
    {
        return $this->tercero;
    }

    /**
     * @param \Buseta\BodegaBundle\Entity\Tercero $tercero
     */
    public function setTercero($tercero)
    {
        $this->tercero = $tercero;
    }

    /**
     * @return \DateTime
     *
     * @deprecated Will be removed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @deprecated Will be removed
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return \HatueySoft\SecurityBundle\Entity\User
     *
     * @deprecated Will be removed
     */
    public function getUpdatedby()
    {
        return $this->updatedby;
    }

    /**
     * @param \HatueySoft\SecurityBundle\Entity\User $updatedby
     *
     * @deprecated Will be removed
     */
    public function setUpdatedby($updatedby)
    {
        $this->updatedby = $updatedby;
    }

    /**
     * @param AlbaranLinea $linea
     *
     * @deprecated Will be removed
     */
    public function addAlbaranLineas(AlbaranLinea $linea)
    {
        $this->albaranLineas->add($linea);
    }

}

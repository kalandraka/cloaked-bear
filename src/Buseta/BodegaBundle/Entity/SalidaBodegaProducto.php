<?php

namespace Buseta\BodegaBundle\Entity;

use Buseta\BodegaBundle\Form\Model\SalidaBodegaProductoModel;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Buseta\BodegaBundle\Validator\Constraints\ValidarSerial;
use Buseta\BodegaBundle\Interfaces\GeneradorBitacoraInterface;

/**
 * SalidaBodegaProducto.
 * @ValidarSerial()
 * @ORM\Table(name="d_salida_bodega_linea")
 * @ORM\Entity(repositoryClass="Buseta\BodegaBundle\Entity\Repository\SalidaBodegaProductoRepository")
 */
class SalidaBodegaProducto implements GeneradorBitacoraInterface
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
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\Producto")
     */
    private $producto;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer")
     * @Assert\NotBlank
     */
    private $cantidad;

    /**
     * @ORM\ManyToOne(targetEntity="Buseta\BodegaBundle\Entity\SalidaBodega", inversedBy="salidas_productos",
     *     cascade={"persist"})
     */
    private $salida;

    /**
     * @var string
     *
     * @ORM\Column(name="seriales", type="string", nullable=true)
     */
    private $seriales;

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
     * Set id
     *
     * @param integer $id
     * @return SalidaBodegaProducto
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return SalidaBodegaProducto
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set producto
     *
     * @param \Buseta\BodegaBundle\Entity\Producto $producto
     * @return SalidaBodegaProducto
     */
    public function setProducto(\Buseta\BodegaBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \Buseta\BodegaBundle\Entity\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set salida
     *
     * @param \Buseta\BodegaBundle\Entity\SalidaBodega $salida
     * @return SalidaBodegaProducto
     */
    public function setSalida(\Buseta\BodegaBundle\Entity\SalidaBodega $salida = null)
    {
        $this->salida = $salida;

        return $this;
    }

    /**
     * Get salida
     *
     * @return \Buseta\BodegaBundle\Entity\SalidaBodega
     */
    public function getSalida()
    {
        return $this->salida;
    }

    /**
     * @return string
     */
    public function getSeriales()
    {
        return $this->seriales;
    }

    /**
     * @param string $seriales
     *
     * @return SalidaBodegaProducto
     */
    public function setSeriales($seriales)
    {
        $this->seriales = $seriales;
        return $this;
    }

    /**
     * Establece los valores desde el modelo en la entidad.
     *
     * @param SalidaBodegaProductoModel $model
     *
     * @return $this
     */
    public function setModelData(SalidaBodegaProductoModel $model)
    {
        $this->setCantidad($model->getCantidad());
        $this->setProducto($model->getProducto());
        $this->setSalida($model->getSalida());

        $seriales = implode(',', $model->getSeriales());
//        $ser_string = '';
//        foreach ($this->getSeriales() as $serial) {
//            if($ser_string != '')
//                $ser_string = $ser_string.",";
//            $ser_string = $ser_string.$serial;
//        }
        $this->setSeriales($seriales);

        return $this;
    }
}

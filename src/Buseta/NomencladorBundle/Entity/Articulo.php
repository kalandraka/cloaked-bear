<?php

namespace Buseta\NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Articulo.
 *
 * @ORM\Table(name="n_articulo")
 * @ORM\Entity
 */
class Articulo extends BaseNomenclador
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
     * @ORM\Column(name="descripcion", type="string")
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="quienpaga", type="string")
     */
    private $quienPaga;

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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Articulo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set quienPaga
     *
     * @param string $quienPaga
     *
     * @return Articulo
     */
    public function setQuienPaga($quienPaga)
    {
        $this->quienPaga = $quienPaga;

        return $this;
    }

    /**
     * Get quienPaga
     *
     * @return string
     */
    public function getQuienPaga()
    {
        return $this->quienPaga;
    }
}

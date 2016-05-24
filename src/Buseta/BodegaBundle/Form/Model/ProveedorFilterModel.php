<?php
namespace Buseta\BodegaBundle\Form\Model;

use Buseta\NomencladorBundle\Entity\Moneda;

class ProveedorFilterModel
{
    /**
     * @var string
     */
    private $nombres;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $cifNif;

    /**
     * @var Moneda
     */
    private $moneda;

    /**
     * @return string
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * @param string $nombres
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
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

}

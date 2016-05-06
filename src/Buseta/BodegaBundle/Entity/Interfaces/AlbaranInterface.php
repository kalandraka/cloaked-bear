<?php

namespace Buseta\BodegaBundle\Entity\Interfaces;


interface AlbaranInterface
{
    /**
     * @return string
     */
    public function getNumeroReferencia();

    /**
     * @return string
     */
    public function getNumeroDocumento();

    /**
     * @return \Buseta\BodegaBundle\Entity\Tercero
     */
    public function getTercero();

    /**
     * @return \DateTime
     */
    public function getFechaMovimiento();

    /**
     * @return \DateTime
     */
    public function getFechaContable();

    /**
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getBodega();

    /**
     * @return string
     */
    public function getEstadoDocumento();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAlbaranLineas();
}

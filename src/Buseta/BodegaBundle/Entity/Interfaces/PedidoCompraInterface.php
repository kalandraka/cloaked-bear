<?php

namespace Buseta\BodegaBundle\Entity\Interfaces;


interface PedidoCompraInterface
{
    /**
     * @return string
     */
    public function getNumeroDocumento();

    /**
     * @return string
     */
    public function getNumeroReferencia();

    /**
     * @return \Buseta\BodegaBundle\Entity\Tercero
     */
    public function getTercero();

    /**
     * @return \DateTime
     */
    public function getFechaPedido();

    /**
     * @return \Buseta\BodegaBundle\Entity\Bodega
     */
    public function getBodega();

    /**
     * @return \Buseta\NomencladorBundle\Entity\Moneda
     */
    public function getMoneda();

    /**
     * @return \Buseta\TallerBundle\Entity\CondicionesPago
     */
    public function getCondicionesPago();

    /**
     * @return string
     */
    public function getEstadoDocumento();

    /**
     * @return float
     */
    public function getDescuento();

    /**
     * @return \Buseta\TallerBundle\Entity\Impuesto
     */
    public function getImpuesto();

    /**
     * @return float
     */
    public function getImporteCompra();

    /**
     * @return float
     */
    public function getImporteTotalLineas();

    /**
     * @return float
     */
    public function getImporteDescuento();

    /**
     * @return float
     */
    public function getImporteImpuesto();

    /**
     * @return float
     */
    public function getImporteTotal();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPedidoCompraLineas();
}

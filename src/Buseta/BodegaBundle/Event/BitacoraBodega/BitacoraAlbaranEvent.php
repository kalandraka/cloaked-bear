<?php

namespace Buseta\BodegaBundle\Event\BitacoraBodega;

use Buseta\BodegaBundle\BusetaBodegaMovementTypes;
use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\BitacoraAlmacen;
use Buseta\BodegaBundle\Model\BitacoraEventModel;

/**
 * Class BitacoraAlbaranEvent
 *
 * @package Buseta\BodegaBundle\Event\BitacoraBodega
 */
class BitacoraAlbaranEvent extends AbstractBitacoraEvent
{
    /**
     * @var Albaran
     */
    private $albaran;


    /**
     * BitacoraAlbaranEvent constructor.
     *
     * @param Albaran $albaran
     * @param boolean $flush
     */
    public function __construct(Albaran $albaran = null)
    {
        parent::__construct();

        if ($albaran !== null && $albaran->getAlbaranLineas()->count() > 0) {
            $this->albaran = $albaran;

            foreach ($albaran->getAlbaranLineas() as $albaranLinea) {
                /** @var AlbaranLinea $albaranLinea */
                $bitacoraEvent = new BitacoraEventModel();
                $bitacoraEvent->setWarehouse($albaran->getBodega());
                $bitacoraEvent->setProduct($albaranLinea->getProducto());
                $bitacoraEvent->setMovementQty($albaranLinea->getCantidadMovida());
                $bitacoraEvent->setMovementDate(new \DateTime());
                $bitacoraEvent->setMovementType(BusetaBodegaMovementTypes::VENDOR_RECEIPTS);
                $bitacoraEvent->setReferencedObject($albaranLinea);
                $bitacoraEvent->setCallback(function (BitacoraAlmacen $bitacoraAlmacen) use ($albaranLinea) {
                    $bitacoraAlmacen->setEntradaSalidaLinea($albaranLinea);
                });
                $this->bitacoraEvents->add($bitacoraEvent);
            }
        }
    }
}

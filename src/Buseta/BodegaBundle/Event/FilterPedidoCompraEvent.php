<?php

namespace Buseta\BodegaBundle\Event;

use Buseta\BodegaBundle\Entity\PedidoCompra;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FilterPedidoCompraEvent
 *
 * @package Buseta\BodegaBundle\Event
 */
class FilterPedidoCompraEvent extends Event
{
    /**
     * @var PedidoCompra
     */
    private $pedidoCompra;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @var boolean
     */
    private $flush;

    /**
     * FilterPedidoCompraEvent constructor
     *
     * @param PedidoCompra $pedidoCompra
     * @param boolean $flush
     */
    function __construct(PedidoCompra $pedidoCompra, $flush=true)
    {
        $this->pedidoCompra = $pedidoCompra;
        $this->error = false;
        $this->flush = $flush;
    }

    /**
     * @return PedidoCompra
     */
    public function getPedidoCompra()
    {
        return $this->pedidoCompra;
    }

    /**
     * @param PedidoCompra $pedidoCompra
     */
    public function setPedidoCompra($pedidoCompra)
    {
        $this->pedidoCompra = $pedidoCompra;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return boolean
     */
    public function isFlush()
    {
        return $this->flush;
    }
}

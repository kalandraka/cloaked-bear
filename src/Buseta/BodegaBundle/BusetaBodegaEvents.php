<?php

namespace Buseta\BodegaBundle;

/**
 * Class BusetaBodegaEvents
 *
 * @package Buseta\BodegaBundle
 */
final class BusetaBodegaEvents
{
    // ALBARAN EVENTS
    const ALBARAN_PRE_CREATE = 'buseta.bodega.albaran.pre_create';

    const ALBARAN_POST_CREATE = 'buseta.bodega.albaran.pos_create';

    const ALBARAN_PRE_PROCESS = 'buseta.bodega.albaran.pre_process';

    const ALBARAN_PROCESS = 'buseta.bodega.albaran_process';

    const ALBARAN_POST_PROCESS = 'buseta.bodega.albaran.pos_process';

    const ALBARAN_PRE_COMPLETE = 'buseta.bodega.albaran.pre_complete';

    const ALBARAN_COMPLETE = 'buseta.bodega.albaran_complete';

    const ALBARAN_POST_COMPLETE = 'buseta.bodega.albaran.pos_complete';

    // NECESIDAD MATERIAL EVENTS
    const NECESIDADMATERIAL_PRE_CREATE = 'buseta.bodega.necesidadmaterial.pre_create';

    const NECESIDADMATERIAL_POST_CREATE = 'buseta.bodega.necesidadmaterial.pos_create';

    const NECESIDADMATERIAL_PRE_PROCESS = 'buseta.bodega.necesidadmaterial.pre_process';

    const NECESIDADMATERIAL_PROCESS = 'buseta.bodega.necesidadmaterial_process';

    const NECESIDADMATERIAL_POST_PROCESS = 'buseta.bodega.necesidadmaterial.pos_process';

    const NECESIDADMATERIAL_PRE_COMPLETE = 'buseta.bodega.necesidadmaterial.pre_complete';

    const NECESIDADMATERIAL_COMPLETE = 'buseta.bodega.necesidadmaterial_complete';

    const NECESIDADMATERIAL_POST_COMPLETE = 'buseta.bodega.necesidadmaterial.pos_complete';


// Pedido Compra EVENTS
    const PEDIDOCOMPRA_PRE_CREATE = 'buseta.bodega.pedidocompra.pre_create';

    const PEDIDOCOMPRA_POST_CREATE = 'buseta.bodega.pedidocompra.pos_create';

    const PEDIDOCOMPRA_PRE_PROCESS = 'buseta.bodega.pedidocompra.pre_process';

    const PEDIDOCOMPRA_PROCESS = 'buseta.bodega.pedidocompra_process';

    const PEDIDOCOMPRA_POST_PROCESS = 'buseta.bodega.pedidocompra.pos_process';

    const PEDIDOCOMPRA_PRE_COMPLETE = 'buseta.bodega.pedidocompra.pre_complete';

    const PEDIDOCOMPRA_COMPLETE = 'buseta.bodega.pedidocompra_complete';

    const PEDIDOCOMPRA_POST_COMPLETE = 'buseta.bodega.pedidocompra.pos_complete';


    // INVENTARIO FISICO EVENTS

    const PHYSICAL_INVENTORY_PRE_CREATE = 'buseta.bodega.inventariofisico.pre_create';

    const PHYSICAL_INVENTORY_POST_CREATE = 'buseta.bodega.inventariofisico.pos_create';

    const PHYSICAL_INVENTORY_PRE_PROCESS = 'buseta.bodega.inventariofisico.pre_process';

    const PHYSICAL_INVENTORY_POST_PROCESS = 'buseta.bodega.inventariofisico.pos_process';

    const PHYSICAL_INVENTORY_PRE_COMPLETE = 'buseta.bodega.inventariofisico.pre_complete';

    const PHYSICAL_INVENTORY_POST_COMPLETE = 'buseta.bodega.inventariofisico.pos_complete';

    // MOVIMIENTO BODEGA

    const MOVEMENT_PRE_CREATE = 'buseta.bodega.movement.pre_create';

    const MOVEMENT_POST_CREATE = 'buseta.bodega.movement.pos_create';

    const MOVEMENT_PRE_PROCESS = 'buseta.bodega.movement.pre_process';

    const MOVEMENT_POST_PROCESS = 'buseta.bodega.movement.pos_process';

    const MOVEMENT_PRE_COMPLETE = 'buseta.bodega.movement.pre_complete';

    const MOVEMENT_POST_COMPLETE = 'buseta.bodega.movement.pos_complete';

    // BITACORA EVENTS
    /**  */
    const BITACORA_CUSTOMER_RETURNS = 'buseta.bodega.bitacora_almacen.customer.returns';
    /**  */
    const BITACORA_CUSTOMER_SHIPMENT = 'buseta.bodega.bitacora_almacen.customer.shipment';
    /**  */
    const BITACORA_INTERNAL_CONSUMPTION = 'buseta.bodega.bitacora_almacen.internal_consumption';
    /**  */
    const BITACORA_INVENTORY_IN_OUT = 'buseta.bodega.bitacora_almacen.inventory.in_out';
    /**  */
    const BITACORA_MOVEMENT_FROM_TO = 'buseta.bodega.bitacora_almacen.movement.from_to';
    /**  */
    const BITACORA_PRODUCTION = 'buseta.bodega.bitacora_almacen.production';
    /**  */
    const BITACORA_VENDOR_RECEIPTS = 'buseta.bodega.bitacora_almacen.vendor.receipts';
    /**  */
    const BITACORA_VENDOR_RETURNS = 'buseta.bodega.bitacora_almacen.vendor.returns';
    /**  */
    const BITACORA_WORK_ORDER = 'buseta.bodega.bitacora_almacen.work_order';

    // BITACORA SERIAL

    const BITACORA_SERIAL_REGISTER_EVENTS = 'buseta.bodega.bitacora_serial.register_events';

    // SALIDA BODEGA EVENTS

    const WAREHOUSE_INOUT_PRE_CREATE = 'buseta.bodega.salidabodega.pre_create';

    const WAREHOUSE_INOUT_POST_CREATE = 'buseta.bodega.salidabodega.post_create';

    const WAREHOUSE_INOUT_PRE_PROCESS = 'buseta.bodega.salidabodega.pre_process';

    const WAREHOUSE_INOUT_POST_PROCESS = 'buseta.bodega.salidabodega.post_process';

    const WAREHOUSE_INOUT_PRE_COMPLETE = 'buseta.bodega.salidabodega.pre_complete';

    const WAREHOUSE_INOUT_POST_COMPLETE = 'buseta.bodega.salidabodega.post_complete';
}

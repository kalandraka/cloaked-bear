<?php
/**
 * Created by PhpStorm.
 * User: anierm
 * Date: 22/12/15
 * Time: 21:27
 */

namespace Buseta\TallerBundle\Event;


final class OrdenTrabajoEvents
{
    //const PROCESAR_ORDEN = 'buseta.taller.orden.procesar';

    const PROCESAR_DIAGNOSTICO = 'buseta.taller.diagnostico.procesarreporte';

    const CAMBIAR_CANCELADO = 'buseta.taller.orden.cambiarcancelado';

    const CAMBIAR_ESTADO_ABIERTO = 'buseta.taller.orden.cambiarestadoabierto';

    const CAMBIAR_ESTADO_PROCESADO = 'buseta.taller.orden.cambiarestadoprocesado';

    const CAMBIAR_ESTADO_CERRADO = 'buseta.taller.orden.cambiarestadocerrado';

    const CAMBIAR_ESTADO_COMPLETADO = 'buseta.taller.orden.cambiarestadocompletado';

    const ORDENTRABAJO_PRE_CREATE = 'buseta.taller.ordentrabajo.pre_create';

    const ORDENTRABAJO_POST_CREATE = 'buseta.taller.ordentrabajo.pos_create';

    const ORDENTRABAJO_PRE_PROCESS = 'buseta.taller.ordentrabajo.pre_process';

    const ORDENTRABAJO_PROCESS = 'buseta.taller.ordentrabajo_process';

    const ORDENTRABAJO_POST_PROCESS = 'buseta.taller.ordentrabajo.pos_process';

    const ORDENTRABAJO_PRE_COMPLETE = 'buseta.taller.ordentrabajo.pre_complete';

    const ORDENTRABAJO_COMPLETE = 'buseta.taller.ordentrabajo_complete';

    const ORDENTRABAJO_POST_COMPLETE = 'buseta.taller.ordentrabajo.pos_complete';

}

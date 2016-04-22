<?php

namespace Buseta\TallerBundle\Event;


final class ReporteEvents
{
    const PROCESAR_SOLICITUD = 'buseta.taller.reporte.procesar';

    const CAMBIAR_ESTADO_PENDIENTE = 'buseta.taller.reporte.cambiarestadopendiente';

    const CAMBIAR_ESTADO_COMPLETADO = 'buseta.taller.reporte.cambiarestadocompletado';

    const CAMBIAR_ESTADO_CANCELADO = 'buseta.taller.reporte.cambiarestadocancelado';

    const REPORTE_PRE_CREATE = 'buseta.taller.reporte.pre_create';

    const REPORTE_POST_CREATE = 'buseta.taller.reporte.pos_create';

    const REPORTE_PRE_PROCESS = 'buseta.taller.reporte.pre_process';

    const REPORTE_PROCESS = 'buseta.taller.reporte_process';

    const REPORTE_POST_PROCESS = 'buseta.taller.reporte.pos_process';

    const REPORTE_PRE_COMPLETE = 'buseta.taller.reporte.pre_complete';

    const REPORTE_COMPLETE = 'buseta.taller.reporte_complete';

    const REPORTE_POST_COMPLETE = 'buseta.taller.reporte.pos_complete';
}

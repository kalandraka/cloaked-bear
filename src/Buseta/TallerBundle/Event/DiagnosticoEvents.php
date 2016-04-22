<?php

namespace Buseta\TallerBundle\Event;


final class DiagnosticoEvents
{


    const CAMBIAR_CANCELADO = 'buseta.taller.diagnostico.cambiarcancelado';

    const PROCESAR_DIAGNOSTICO = 'buseta.taller.diagnostico.crear';

    const CAMBIAR_ESTADO_CO = 'buseta.taller.diagnostico.cambiarestadoco';

    const CAMBIAR_ESTADO_PR = 'buseta.taller.diagnostico.cambiarestadopr';

    const DIAGNOSTICO_PRE_CREATE = 'buseta.taller.diagnostico.pre_create';

    const DIAGNOSTICO_POST_CREATE = 'buseta.taller.diagnostico.pos_create';

    const DIAGNOSTICO_PRE_PROCESS = 'buseta.taller.diagnostico.pre_process';

    const DIAGNOSTICO_PROCESS = 'buseta.taller.diagnostico_process';

    const DIAGNOSTICO_POST_PROCESS = 'buseta.taller.diagnostico.pos_process';

    const DIAGNOSTICO_PRE_COMPLETE = 'buseta.taller.diagnostico.pre_complete';

    const DIAGNOSTICO_COMPLETE = 'buseta.taller.diagnostico_complete';

    const DIAGNOSTICO_POST_COMPLETE = 'buseta.taller.diagnostico.pos_complete';

}
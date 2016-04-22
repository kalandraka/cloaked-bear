<?php

namespace Buseta\NotificacionesBundle;


final class NotificacionesVars {

    /**
     * @return array
     */
    public static function getDefaultNotificacion()
    {
        return array(
            'notificacion_interna' => 'Notificación Interna',
            'notificacion_correo' => 'Notificación por correo'
        );
    }
} 
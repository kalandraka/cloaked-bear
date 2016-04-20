<?php

namespace HatueySoft\NotificacionesBundle;


final class NotificacionesVars {

    /**
     * @return array
     */
    public static function getDefaultNotificacion()
    {
        return array(
            'notificacion_interna' => 'Notificación interna',
            'notificacion_correo' => 'Notificación por correo',
            'notificacion_sms' => 'Notificación por sms'
        );
    }
} 
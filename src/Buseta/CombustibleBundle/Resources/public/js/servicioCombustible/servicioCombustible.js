TallerApp.namespace('combustible.servicioCombustible');

TallerApp.combustible.servicioCombustible = (function (App, $) {
    "use strict";
    var
        selectBoleta = 'select#combustible_servicio_combustible_boleta',
        selectVehiculo = 'select#combustible_servicio_combustible_vehiculo',
        selectChofer = 'select#combustible_servicio_combustible_chofer_chofer',
        marchamo1 = 'input#combustible_servicio_combustible_marchamo1, label[for="combustible_servicio_combustible_marchamo1"]',
        marchamo2 = 'input#combustible_servicio_combustible_marchamo2, label[for="combustible_servicio_combustible_marchamo2"]',

        init = function () {
            $(selectBoleta).chosen({
                allow_single_deselect: true
            });

            $(selectVehiculo).chosen();
            $(selectVehiculo).on('change', function (e) {
                checkChoferVehiculo();
            });

            $(selectChofer).chosen();
            $(selectChofer).on('change', function (e) {
                checkChoferVehiculo();
            });

            checkChoferVehiculo();
        },

        checkChoferVehiculo = function () {
            var checkChofer = $(selectChofer).val(),
                checkVehiculo = $(selectVehiculo).val();

            if (checkChofer !== '' && checkVehiculo !== '') {
                $(marchamo1).show(200);
                $(marchamo2).show(200);
            } else {
                $(marchamo1).hide(200);
                $(marchamo2).hide(200);
            }
        }
    ;

    return App.servicioCombustible = {
        init: init
    };
}(window.TallerApp, window.jQuery));

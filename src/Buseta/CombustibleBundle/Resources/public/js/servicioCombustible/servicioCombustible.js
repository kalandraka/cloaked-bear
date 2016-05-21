TallerApp.namespace('Combustible.servicioCombustible');

TallerApp.Combustible.servicioCombustible = (function (App, $) {
    "use strict";

    var
        form, selectBoleta, selectVehiculo, selectChofer, marchamo1, marchamo2, boletaApiServerAddress,

        Constr = function (form_id, _boletaApiServerAddress) {
            form = form_id;
            selectBoleta = 'select#' + form + '_boleta';
            selectVehiculo = 'select#' + form + '_vehiculo';
            selectChofer = 'select#' + form + '_chofer_chofer';
            marchamo1 = 'input#' + form + '_marchamo1, label[for="' + form + '_marchamo1"]';
            marchamo2 = 'input#' + form + '_marchamo2, label[for="' + form + '_marchamo2"]';
            boletaApiServerAddress = _boletaApiServerAddress;

            init();
        },

        findBoletaApi = function () {
            var boleta_id = $(selectBoleta).val();
            $.ajax({
                type: 'GET',
                url: 'http://' + boletaApiServerAddress + '/boleta/api/choferAndAutobusFromBoleta?identificador=' + boleta_id,
                success: function (data) {
                    //var values = $.parseJSON(data);
                    var values = data;
                    var _selectChofer = $(selectChofer);
                    var _selectVehiculo = $(selectVehiculo);

                    var requestData = {
                        cedula_chofer: values.chofer.cedula,
                        numero_bus: values.autobus.identificador
                    };

                    $.ajax({
                        type: 'GET',
                        url: Routing.generate('chofer_bus_ajax'),
                        data: requestData,
                        success: function (data) {
                            //var values = $.parseJSON(data);
                            var values = data;
                            _selectChofer
                                .val(values.chofer)
                                .trigger("chosen:updated")
                                .trigger("change");
                            _selectVehiculo
                                .val(values.autobus)
                                .trigger("chosen:updated")
                                .trigger('change');
                        }
                    });
                }
            });
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
        },

        init= function () {
            $(selectBoleta).chosen({
                allow_single_deselect: true
            });
            $(selectBoleta).on('change', function (e) {
                e.preventDefault();

                findBoletaApi();
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
        };

        Constr.prototype = {
            constructor: TallerApp.Combustible.servicioCombustible,
            version:'1.0'
        };

    return Constr;
}(window.TallerApp, window.jQuery));

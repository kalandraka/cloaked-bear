TallerApp.namespace('Empleados.empleado');

TallerApp.Empleados.empleado = (function (App, $) {
    var
        fechaNacimiento,
        form,
        Constr = function (_form) {
            form = _form;
            fechaNacimiento = '#' + form + '_fechaNacimiento';

            this.init();
        };

    Constr.prototype = {
        constructor: TallerApp.Empleados.empleado,
        version: "1.0",

        init: function () {
            $(fechaNacimiento).datetimepicker({
                'format': 'DD/MM/YYYY',
                'useCurrent': false
            });
        }
    };

    return Constr;
}(window.TallerApp, window.jQuery));

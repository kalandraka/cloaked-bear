var necesidadmaterial = {

    form_id: $('div#basicos').find('form').attr('id'),
    form_name: $('div#basicos').find('form').attr('name'),
    id: '',
    _start_evens: function () {
        $('#bodega_necesidad_material_fecha_pedido').datetimepicker({
            'format': 'DD/MM/YYYY'
        });
        $('#bodega_necesidad_material_tercero').chosen();
        $('#bodega_necesidad_material_almacen').chosen();

        $('a#btn_necesidadmaterial_save').on('click', necesidadmaterial._save);
        $('#bodega_necesidad_material_tercero').on('change', necesidadmaterial._updateMoneda);
    },
    _load: function () {
        necesidadmaterial.id = $('input[id="' + necesidadmaterial.form_id + '_id"]').val();
        if (necesidadmaterial.id === '' || necesidadmaterial.id === undefined) {
            // hide all tabs on page load
            $('a[data-toggle="tab"]').each(function(key, item) {
                if($(item).attr('href') != '#basicos') {
                    $(item).parent().hide();
                }
            });
        }

        // on show tab events
        $('a[data-toggle="tab"]').unbind('show.bs.tab');
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var href = $(e.target).attr('href');
            var relhref = $(e.relatedTarget).attr('href');

            if (href === '#lineas') {
                lineas._load();
            }
        });

        necesidadmaterial._start_evens();
    },
    _save: function (event) {
        if (event != undefined) {
            event.preventDefault();
        }

        // disable btn
        button._disable('a#btn_necesidadmaterial_save');

        // add spinning to show loading process
        tabs._add_loadding('basicos');

        // PedidoCompra Id
        necesidadmaterial.id = $('input[id="' + necesidadmaterial.form_id + '_id"]').val();

        var necesidadmaterialsForm    = $('form#' + necesidadmaterial.form_id),
            url             = Routing.generate('necesidadmaterial_create',{});
        if(necesidadmaterial.id !== '' && necesidadmaterial.id !== undefined) {
            url = Routing.generate('necesidadmaterial_update', {'id': necesidadmaterial.id});
        }

        necesidadmaterialsForm.ajaxSubmit({
            success: necesidadmaterial._done,
            error: utils._fail,
            complete: necesidadmaterial._always,
            uploadProgress: necesidadmaterial._upload,
            url: url,
            dataType: 'json'
        });
    },
    _done: function (response, textStatus, jqXHR) {
        $('form#' + necesidadmaterial.form_id).replaceWith(response.view);
        if(jqXHR.status == 201) {
            $btalerts.addSuccess(response.message);
            // PedidoCompra Id
            necesidadmaterial.id = $('input[id="' + necesidadmaterial.form_id + '_id"]').val();
            // activate all tabs
            tabs._show_all_tabs();
        } else if(jqXHR.status == 202) {
            $btalerts.addSuccess(response.message);
        }
        necesidadmaterial._start_evens();
    },
    _always: function() {
        // remove spinning
        tabs._remove_loadding('basicos');
        // remove progress bar
        progressBar._remove_progressBar($('input[id="' + necesidadmaterial.form_id + '_foto_file"]').attr('id'));

        button._enable('a#btn_necesidadmaterial_save');
    },
    _upload: function (event, position, total, percentComplete) {
        var progressBarr = $('div#basicos').find('.progress-bar')[0];
        if (progressBarr !== undefined) {
            $(progressBarr).css('width', percentComplete + '%');
            $(progressBarr).find('span').html(percentComplete + '% Completado');
        }
    },
    _updateMoneda: function(){
        var data = {
            tercero_id: $(this).val()
        };

        $.ajax({
            type: 'GET',
            url: Routing.generate('necesidadMaterial_ajax_proveedor_moneda'),
            data: data,
            success: function(data) {
                var moneda = $.parseJSON(data);
                if (moneda != undefined && moneda != null) {
                    $('#bodega_necesidad_material_moneda').val(moneda.id);
                }
            }
        });
    }
};

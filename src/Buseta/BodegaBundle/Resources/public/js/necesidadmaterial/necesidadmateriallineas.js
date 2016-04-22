var Totales = function () {
    this.lineas = [];
    this.total = 0;

    this.addLinea = function(monto) {
        monto = parseInt(monto);
        this.lineas.push({monto: monto});
        this.total += monto;
    };

    /*this.addLinea = function(id, monto) {
     this.lineas.push({id: id, monto: monto});
     this.monto += monto;
     };*/

    this.removeLinea = function ( id ) {
        var aux = [];
        for ( var i = 0; i < this.lineas.length; i++ ) {
            if( this.lineas[i].id !== id ) {
                aux.push(this.lineas[i]);
            } else {
                this.total -= this.lineas[i].monto;
            }
        }

        this.lineas = aux;
    };

    this.countTotal = function () {
        this.total = 0;
        for ( var i = 0 ; i < this.lineas.length ; i++ ) {
            var monto = this.lineas[i].monto;
            this.total += parseInt(monto);
        }

        return this.total;
    };

    this.getTotal = function () {
        return this.total;
    }
};
var lineastotales = new Totales();

var lineas = {
    form_name: '',
    form_id: '',
    id: '',
    /**
     * Inicia los eventos en el listado de lineas para el pedido de compra
     * @private
     */
    _start_events: function () {
        $('a[href="#form_lineas_modal"]').on('click', lineas._load_modal);
        // Paginator sort
        $('table.lineas_records_list').find('a.sortable, a.asc, a.desc').on('click', lineas._load);
        // Table addresses actions
        $('table.lineas_records_list').find('a[href="#edit"]').on('click', lineas._load_modal);
        $('table.lineas_records_list').find('a[href="#delete"]').on('click', lineas._load_delete_modal);
        // Paginator navigation
        $('div.lineas-paginator.row ul.pagination').find('a.paginator-link').on('click', lineas._load);
    },
    /**
     * Carga el listado de lineas para el pedido de compra
     * @param event
     * @private
     */
    _load: function (event) {
        if(event !== undefined) {
            event.preventDefault();
        }

        if (necesidadmaterial.id == '') {
            return;
        }

        // add spinning to show loading process
        tabs._add_loadding('lineas');

        var url = Routing.generate('necesidadmaterial_lineas_list',{'necesidadmaterial': necesidadmaterial.id});
        if($(this).hasClass('sortable') || $(this).hasClass('desc') || $(this).hasClass('asc') || $(this).hasClass('paginator-link')) {
            url = $(this).attr('href');
        }

        $.get(url).done(function (response, textStatus, jqXHR) {
            $('div#lineas').html(response);

            lineas._start_events();
        }).fail(utils._fail).always(lineas._always);
    },
    /**
     * Carga el modal para crear/editar una linea
     * @param event
     * @private
     */
    _load_modal: function(event) {
        if(event !== undefined) {
            event.preventDefault();
        }

        if(necesidadmaterial.id === '' || necesidadmaterial.id === undefined) {
            return;
        }

        var url = Routing.generate('necesidadmaterial_lineas_new_modal', {'necesidadmaterial': necesidadmaterial.id});
        if($(this).attr('href') !== undefined && $(this).attr('href') === '#edit') {
            url = Routing.generate('necesidadmaterial_lineas_edit_modal', {'necesidadmaterial': necesidadmaterial.id, id:$(this).data('content')});
        }

        $.get(url)
            .done(function(response, textStatus, jqXHR){
                $('div#form_lineas_modal').replaceWith($(response.view));

                lineas.form_id = $('div#form_lineas_modal').find('form').attr('id');
                lineas.form_name = $('div#form_lineas_modal').find('form').attr('name');

                $('div#form_lineas_modal').modal('show');
                lineas._linea_start_events();
            }).fail(utils._fail).always(function(){});
    },
    /**
     * Actualiza los eventos para el modal de lineas
     * @private
     */
    _linea_start_events: function () {
        $('a#btn_lineas_save').unbind('click');
        $('a#btn_lineas_save').on('click',  lineas._save_modal);

        $('a#btn_lineas_cancel').unbind('click');
        $('a#btn_lineas_cancel').on('click', function(){
            $('div#form_lineas_modal').modal('hide');
        });

        //Al presionar el boton de actualizacion de datos del producto
        //se deben recargar los valores relacionados con el producto seleccionado
        $('#actualizar_productos').unbind('click');
        $('#actualizar_productos').click(function () {
            lineas._get_product_data();
        });

        // Chosen
        $('#' + lineas.form_id + '_producto').chosen({ alt_search: true });
        $('#' + lineas.form_id + '_producto').bind('change', lineas._get_product_data);

        $('#' + lineas.form_id + '_cantidad_pedido').bind('keyup change', lineas._update_importe_linea);
        $('#' + lineas.form_id + '_impuesto').bind('change', lineas._update_importe_linea);
        $('#' + lineas.form_id + '_porciento_descuento').bind('keyup change', lineas._update_importe_linea);
        $('#' + lineas.form_id + '_precio_unitario').bind('keyup change', lineas._update_importe_linea);
    },
    /**
     * Carga el modal para eliminar una linea
     * @param event
     * @private
     */
    _load_delete_modal: function(event) {
        if(event !== undefined) {
            event.preventDefault();
        }

        if(necesidadmaterial.id === '' || necesidadmaterial.id === undefined) {
            return;
        }

        var id  = $(this).data('content'),
            url = Routing.generate('necesidadmaterial_lineas_delete', {id: id});
        $.get(url)
            .done(function(response, textStatus, jqXHR){
                $('div#form_necesidadmateriallinea_delete_modal').replaceWith($(response.view));

                $('div#form_necesidadmateriallinea_delete_modal a#btn_necesidadmateriallinea_delete').on('click', lineas._save_delete_modal);
                $('div#form_necesidadmateriallinea_delete_modal a#btn_necesidadmateriallinea_cancel').on('click', function(){
                    $('div#form_necesidadmateriallinea_delete_modal').modal('hide');
                });

                $('div#form_necesidadmateriallinea_delete_modal').modal('show');
            }).fail(utils._fail).always(function(){});
    },
    /**
     * Salva el modal para crear/editar una linea
     * @param event
     * @private
     */
    _save_modal: function (event) {
        if(event != undefined) {
            event.preventDefault();
        }

        $('#btn_lineas_save').find('span')
            .removeClass('glyphicon')
            .removeClass('glyphicon-save')
            .addClass('fa')
            .addClass('fa-gear')
            .addClass('fa-spin');

        //var url = Routing.generate('necesidadmaterial_lineas_new_modal',{'necesidadmaterial': necesidadmaterial.id}),
        //    id  = $('#' + lineas.form_id + '_id').val();
        //if(id !== '' && id !== undefined) {
        //    url = Routing.generate('necesidadmaterial_lineas_edit_modal',{'necesidadmaterial': necesidadmaterial.id, id: id});
        //}

        //Actualiza las nuevas lineas insertadas
        $('form#' + lineas.form_id).ajaxSubmit({
            success: lineas._done,
            error: utils._fail,
            complete: lineas._always,
            dataType: 'json'
        });
    },
    /**
     * Envia el formulario del modal para eliminar una linea
     * @param event
     * @private
     */
    _save_delete_modal: function (event) {
        if(event != undefined) {
            event.preventDefault();
        }

        $('#btn_lineas_delete').find('span')
            .removeClass('glyphicon')
            .removeClass('glyphicon-trash')
            .addClass('fa')
            .addClass('fa-gear')
            .addClass('fa-spin');

        var deleteForm = $('div#form_necesidadmateriallinea_delete_modal').find('form'),
            url = $(deleteForm).attr('action');

        deleteForm.ajaxSubmit({
            success: function (response, textStatus, jqXHR) {
                if(jqXHR.status == 202) {
                    $btalerts.addSuccess(response.message);
                }
                $('div#form_necesidadmateriallinea_delete_modal').modal('hide');

                lineas._load();
                lineas._update_necesidadmaterial();
            },
            error: utils._fail,
            complete: lineas._always,
            url: url,
            dataType: 'json'
        });
    },
    _done: function (response, textStatus, jqXHR) {
        $('form#' + lineas.form_id).replaceWith($(response.view).find('form'));

        if(jqXHR.status == 201 || jqXHR.status == 202) {
            $btalerts.addSuccess(response.message);

            $('div#form_lineas_modal').modal('hide');

            lineas._load();
            lineas._update_necesidadmaterial();
            //Actualiza los valores de los campos Importes Total y Total por Lineas
            //var monto = $('#' + lineas.form_id + '_importe_linea').val();
            //
            //lineastotales.addLinea(monto);
            //var montototal = lineastotales.getTotal();
            //$('#bodega_necesidad_material_importe_total_lineas').val(montototal);
            //$('#bodega_necesidad_material_importe_total').val(montototal);
            //--Actualiza los valores de los campos Importes Total y Total por Lineas
        } else {
            lineas._linea_start_events();
        }
    },
    _always: function(jqXHR, textStatus) {
        // remove spinning
        tabs._remove_loadding('lineas');
        $('a[id^="btn_lineas_"]').find('span')
            .addClass('glyphicon')
            .addClass('glyphicon-save')
            .removeClass('fa')
            .removeClass('fa-gear')
            .removeClass('fa-spin');
    },
    /**
     * Actualiza el PedidoCompra con los nuevos valores de los importes de lineas y totales
     */
    _update_necesidadmaterial: function () {
        url = Routing.generate('necesidadmaterial_update', {'id': necesidadmaterial.id});

        $('form#' + necesidadmaterial.form_id).ajaxSubmit({
            success: necesidadmaterial._done,
            error: utils._fail,
            complete: necesidadmaterial._always,
            url: url,
            dataType: 'json'
        });
    },
    /**
     * Obtiene por linea los valores para el costo, unidad de medida y actualiza el importe de linea por el producto
     */
    _get_product_data: function(){
        var producto_id = $('#' + lineas.form_id + '_producto').val();
        $.getJSON(Routing.generate('productos_get_product_data', {'id': producto_id}), function (data) {
            var tbody = 'table#producto_proveedores_results_list',
                provider, code, cost, select, tr, count = 0;

            var ep = '#editar_producto';
            $(ep).find('[data-content]').remove();

            var editar_producto = $('#editar_producto');

            $(tbody).find('tr[data-content]').remove();

            //Boton para editar el producto seleccionado en una nueva pestaña del sistema
            producto = $('<a>', { 'href': Routing.generate('productos_producto_edit', {'id': producto_id}), 'target': '_blank', 'class': 'form-control btn btn-danger btn-xl', 'value': 'Editar', 'style': 'margin-bottom: 3px', 'data-action':'#edit', 'data-content': producto_id});
            span = $('<span>', {'class': 'glyphicon glyphicon-edit'}).text(' Modificar');
            producto.append(span);
            editar_producto.append(producto);

            $.each(data.costos, function(id, costo) {
                if (costo.proveedor != undefined) {
                    provider    = $('<td>').text(costo.proveedor.nombre).append($('<input>',{type: 'hidden', value: costo.proveedor.id}));
                } else {
                    provider    = $('<td>').text('-');
                }
                code        = $('<td>').text(costo.codigo != undefined ? costo.codigo : '-');
                cost        = $('<td>', {'data-action':'#edit', 'data-content': id}).text(costo.costo);
                select      = $('<td>',{class: 'text-center', style: 'width: 1%;'}).html('<a href="#cost"><span class="fa fa-check"></span></a>');

                tr = $('<tr>',{'data-content': true});
                tr.append(provider)
                    .append(code)
                    .append(cost)
                    .append(select);

                $(tbody).append(tr);
                count++;
            });
            count > 0 ? $(tbody).show() : $(tbody).hide();
            $(tbody).find('a[href="#cost"]').on('click', lineas._select_product_cost);
            $(tbody).find('td[data-action="#edit"]').on('click', lineas._edit_product_cost);

            if (data.uom != undefined && data.uom != null) {
                $('#' + lineas.form_id + '_uom').val(data.uom.id);
            }

            lineas._update_importe_linea();
        });
    },
    _select_product_cost: function (event){
        event.preventDefault();

        var $this = $(this),
            costo = $this.parent().prev().text();

        if (costo != undefined && costo != null) {
            $('#' + lineas.form_id + '_precio_unitario').val(costo);
        }

        //obtener la moneda del proveedor seleccionado
        var terceroID = $this.parent().prev().prev().prev().children().val();
        if (terceroID != undefined && terceroID != null) {
            var data = {
                tercero_id: terceroID
            };

            $.ajax({
                type: 'GET',
                url: Routing.generate('necesidadMaterial_ajax_proveedor_moneda'),
                data: data,
                success: function (data) {
                    var moneda = $.parseJSON(data);
                    if (moneda != undefined && moneda != null) {
                        $('#buseta_bodegabundle_necesidad_material_linea_moneda').val(moneda.id);
                    }
                }
            });
        }
    },
    _edit_product_cost: function (event) {
        var $this = $(this),
            value = $this.text(),
            input = $('<input>',{class:'form-control', 'data-prev-value': value})
                .val(value)
                .bind('blur keyup', lineas._update_product_cost),
            div   = $('<div>', {class: 'form-group', style: 'margin: 0;'}).append(input);

        $this.unbind('click');
        $this.html(div);
        $this.find('input').focus();
    },
    _update_product_cost: function (event) {
        var $this           = $(this),
            value           = $this.val(),
            div             = $this.parent(),
            error_icon      = $('<span>', {class: 'fa fa-times form-control-feedback', 'aria-hidden': true, style: 'top: 0;'}),
            loading_icon    = $('<span>', {class: 'fa fa-gear fa-spin form-control-feedback', 'aria-hidden': true, style: 'top: 0;'}),
            help            = $('<p>',{class: 'help-block', style: 'margin-bottom: 5px;'}),
            td              = div.parent();

        $this.parent()
            .removeClass('has-error')
            .removeClass('has-feedback')
            .find('span[class*="form-control-feedback"]')
            .remove();
        div.find('p.help-block').remove();

        if (event.type == "blur" || (event.type == "keyup" && event.keyCode == 13)) {
            if ($this.val().length === 0) {
                help.text('El valor no debe estar vacío.');
                div.addClass('has-error')
                    .addClass('has-feedback')
                    .append(error_icon)
                    .append(help);

                return false;
            }

            if (!$.isNumeric(value)) {
                help.text('El valor debe ser un número válido.');
                div.addClass('has-error')
                    .addClass('has-feedback')
                    .append(error_icon)
                    .append(help);

                return false;
            }
            div.addClass('has-success')
                .addClass('has-feedback')
                .append(loading_icon);

            $.ajax({
                url: Routing.generate('producto_costo_update_from_registro_compra', {id: td.data('content')}),
                data: {
                    costo: value
                },
                method: 'PUT'
            }).done(function (data, statusText, jqXHR) {
                if (jqXHR.status == 202) {
                    //var json = JSON.parse(data);
                    help.text('Se han salvado los datos.');
                    div.find('p.help-block')
                        .remove();
                    div.append(help)
                        .find('span[class*="form-control-feedback"]')
                        .removeClass('fa-gear')
                        .removeClass('fa-spin')
                        .addClass('fa-check');

                    setTimeout(function (){
                        td.html(value);
                        td.on('click', lineas._edit_product_cost);
                    }, 2000);
                }
            }).fail(function () {
                div.removeClass('has-succes')
                    .addClass('has-error')
                    .find('p.help-block')
                    .remove();

                help.text('Ha ocurrido un error.');
                div.find('span[class*="form-control-feedback"]')
                    .remove();
                div.append(error_icon)
                    .append(help);

                setTimeout(function (){
                    td.html(value);
                    td.on('click', lineas._edit_product_cost);
                }, 2000);
            });
        } else if(event.type == "keyup" && event.keyCode == 27) {
            td.html($this.data('prev-value'));
            td.on('click', lineas._edit_product_cost);

            event.preventDefault();
            event.stopPropagation();
        }
    },
    _update_importe_linea: function () {
        var $importeLinea        = $('#' + lineas.form_id + '_importe_linea'),
            $impuesto            = $('#' + lineas.form_id + '_impuesto'),
            cantidadPedido      = $('#' + lineas.form_id + '_cantidad_pedido').val(),
            costoUnitario       = $('#' + lineas.form_id + '_precio_unitario').val(),
            porcientoDescuento  = $('#' + lineas.form_id + '_porciento_descuento').val(),
            importeTotal        = 0,
            importeImpuesto     = 0;

        if (cantidadPedido == undefined || cantidadPedido == null) {
            cantidadPedido = 0;
        }

        var bruto = Number(cantidadPedido) * Number(costoUnitario);
        var importeDescuento = Number(bruto) * Number(porcientoDescuento) / 100;

        if($impuesto.val() != undefined && $impuesto.val() != null && $impuesto.val() != '') {
            var tarifa  = $impuesto.find(':selected').data('tarifa'),
                tipo    = $impuesto.find(':selected').data('tipo');
            if (tipo == 'porcentaje') {
                importeImpuesto = Number(bruto) * Number(tarifa) / 100;
            } else {
                importeImpuesto = tarifa;
            }
        }

        importeTotal = Number(bruto) + Number(importeImpuesto) - Number(importeDescuento);
        if(!isNaN(importeTotal)) {
            $importeLinea.val(importeTotal);
        } else {
            $importeLinea.val(0);
        }

    }
};

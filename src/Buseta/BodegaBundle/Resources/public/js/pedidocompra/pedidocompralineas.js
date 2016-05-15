var
    Totales = function () {
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
    },

    lineas = {
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

            if (pedidocompra.id == '') {
                return;
            }

            // add spinning to show loading process
            tabs._add_loadding('lineas');

            var url = Routing.generate('pedidocompra_lineas_list',{'pedidocompra': pedidocompra.id});
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
            $('a[href="#form_lineas_modal"]').addClass('disabled');
            if(event !== undefined) {
                event.preventDefault();
            }

            if(pedidocompra.id === '' || pedidocompra.id === undefined) {
                return;
            }

            var url = Routing.generate('pedidocompra_lineas_new_modal', {'pedidocompra': pedidocompra.id});
            if($(this).attr('href') !== undefined && $(this).attr('href') === '#edit') {
                url = Routing.generate('pedidocompra_lineas_edit_modal', {'pedidocompra': pedidocompra.id, id:$(this).data('content')});
            }

            $.get(url)
                .done(function(response, textStatus, jqXHR){
                    $('div#form_lineas_modal').replaceWith($(response.view));

                    lineas.form_id = $('div#form_lineas_modal').find('form').attr('id');
                    lineas.form_name = $('div#form_lineas_modal').find('form').attr('name');

                    $('div#form_lineas_modal').modal('show');
                    $('a[href="#form_lineas_modal"]').removeClass('disabled');
                    lineas._modal_start_events();
                }).fail(utils._fail).always(function(){
                    $('a[href="#form_lineas_modal"]').removeClass('disabled');
                });
        },
        /**
         * Actualiza los eventos para el modal de lineas
         * @private
         */
        _modal_start_events: function () {
            $('a#btn_lineas_save').off('click');
            $('a#btn_lineas_save').on('click',  lineas._save_modal);

            $('a#btn_lineas_cancel').off('click');
            $('a#btn_lineas_cancel').on('click', function(){
                $('div#form_lineas_modal').modal('hide');
            });

            //Al presionar el boton de actualizacion de datos del producto
            //se deben recargar los valores relacionados con el producto seleccionado
            $('#actualizar_productos').off('click');
            $('#actualizar_productos').on('click', lineas._get_product_data);

            // Chosen
            $('#' + lineas.form_id + '_producto').chosen({ alt_search: true });
            $('#' + lineas.form_id + '_producto').on('change', lineas._get_product_data);

            chosen_ajaxify(lineas.form_id + '_producto', 'autocompletar_producto_ajax');


            function chosen_ajaxify(id, ajax_url){
                $('div#' + id + '_chosen .chosen-search input').keyup(function(event){
                    var keyword = $(this).val();
                    var keyword_pattern = new RegExp(keyword, 'gi');
                    if (keyword.length > 3 && event.keyCode != 13 && event.keyCode != 8 && event.keyCode != 27
                        && event.keyCode > 33 && event.keyCode > 46){
                        $('div#' + id + '_chosen ul.chosen-results').empty();
                        $("#"+id).empty();
                        $.ajax({
                            url: Routing.generate(ajax_url, {'cad': keyword}),
                            dataType: "json",
                            success: function(response){
                                // map, just as in functional programming :). Other way to say "foreach"
                                $.map(response, function(item){
                                    var alt_search = item.codigoATSA;
                                    if (item.codigoA != null && item.codigoA != ''){
                                        alt_search = alt_search + ' ' + item.codigoA;
                                    }
                                    if (item.codigoCostos != null && item.codigoCostos != ''){
                                        alt_search = alt_search + ' ' + item.codigoCostos;
                                    }
                                    $('#'+id).append('<option data-alt-search="' + alt_search + '" value="' + item.id + '">' + '['+item.codigoATSA+'] ' + item.nombre + '</option>');
                                });
                                $("#"+id).trigger("chosen:updated");
                                $('div#' + id + '_chosen').removeClass('chosen-container-single-nosearch');
                                $('div#' + id + '_chosen .chosen-search input').val(keyword);
                                $('div#' + id + '_chosen .chosen-search input').removeAttr('readonly');
                                $('div#' + id + '_chosen .chosen-search input').focus();
                                // put that underscores
                                $('div#' + id + '_chosen .active-result').each(function(){
                                    var html = $(this).html();
                                    $(this).html(html.replace(keyword_pattern, function(matched){
                                        return '<em>' + matched + '</em>';
                                    }));
                                });
                            }
                        });
                    }
                });
            }

            $('#' + lineas.form_id + '_cantidad_pedido').on('keyup change', lineas._update_importe_linea);
            $('#' + lineas.form_id + '_impuesto').on('change', lineas._update_importe_linea);
            $('#' + lineas.form_id + '_porciento_descuento').on('keyup change', lineas._update_importe_linea);
            $('#' + lineas.form_id + '_precio_unitario').on('keyup change', lineas._update_importe_linea);

            // trigger find uom by selected product event
            producto.findUOM();
        },
        /**
         * Carga el modal para eliminar una linea
         * @param event
         * @private
         */
        _load_delete_modal: function(event) {
            $('a[href="#delete"]').addClass('disabled');
            if(event !== undefined) {
                event.preventDefault();
            }

            if(pedidocompra.id === '' || pedidocompra.id === undefined) {
                return;
            }

            var id  = $(this).data('content'),
                url = Routing.generate('pedidocompra_lineas_delete', {id: id});
            $.get(url)
                .done(function(response, textStatus, jqXHR){
                    $('div#form_pedidocompralinea_delete_modal').replaceWith($(response.view));

                    $('div#form_pedidocompralinea_delete_modal a#btn_pedidocompralinea_delete').on('click', lineas._save_delete_modal);
                    $('div#form_pedidocompralinea_delete_modal a#btn_pedidocompralinea_cancel').on('click', function(){
                        $('div#form_pedidocompralinea_delete_modal').modal('hide');
                    });

                    $('div#form_pedidocompralinea_delete_modal').modal('show');
                    $('a[href="#delete"]').removeClass('disabled');
                }).fail(utils._fail).always(function(){
                    $('a[href="#delete"]').removeClass('disabled');
                });
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

            //var url = Routing.generate('pedidocompra_lineas_new_modal',{'pedidocompra': pedidocompra.id}),
            //    id  = $('#' + lineas.form_id + '_id').val();
            //if(id !== '' && id !== undefined) {
            //    url = Routing.generate('pedidocompra_lineas_edit_modal',{'pedidocompra': pedidocompra.id, id: id});
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

            var deleteForm = $('div#form_pedidocompralinea_delete_modal').find('form'),
                url = $(deleteForm).attr('action');

            deleteForm.ajaxSubmit({
                success: function (response, textStatus, jqXHR) {
                    if(jqXHR.status == 202) {
                        $btalerts.addSuccess(response.message);
                    }
                    $('div#form_pedidocompralinea_delete_modal').modal('hide');

                    lineas._load();
                    lineas._update_pedidocompra();
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
                lineas._update_pedidocompra();
                //Actualiza los valores de los campos Importes Total y Total por Lineas
                //var monto = $('#' + lineas.form_id + '_importe_linea').val();
                //
                //lineastotales.addLinea(monto);
                //var montototal = lineastotales.getTotal();
                //$('#bodega_pedido_compra_importe_total_lineas').val(montototal);
                //$('#bodega_pedido_compra_importe_total').val(montototal);
                //--Actualiza los valores de los campos Importes Total y Total por Lineas
            } else {
                lineas._modal_start_events();
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
        _update_pedidocompra: function () {
            url = Routing.generate('pedidocompra_update', {'id': pedidocompra.id});

            $('form#' + pedidocompra.form_id).ajaxSubmit({
                success: pedidocompra._done,
                error: utils._fail,
                complete: pedidocompra._always,
                url: url,
                dataType: 'json'
            });
        },
        /**
         * Obtiene por linea los valores para el costo, unidad de medida y actualiza el importe de linea por el producto
         */
        _get_product_data: function(){
            var producto_id = $('#' + lineas.form_id + '_producto').val();

            producto.findUOM();

            $.getJSON(Routing.generate('productos_get_product_data', {'id': producto_id}), function (data) {
                var tbody = 'table#producto_proveedores_results_list',
                    _producto, span, provider, code, cost, select, tr, count = 0;

                var editar_producto = $('#editar_producto');

                $(tbody).find('tr[data-content]').remove();

                //Boton para editar el producto seleccionado en una nueva pestaña del sistema
                if (editar_producto.find('a[data-action="#edit"]').size() === 0) {
                    _producto = $('<a>', {
                        'href': Routing.generate('productos_producto_edit', {'id': producto_id}),
                        'target': '_blank',
                        'class': 'btn btn-primary btn-xs pull-right',
                        'value': 'Editar',
                        'style': 'margin-bottom: 3px',
                        'data-action':'#edit',
                        'data-content': producto_id,
                        'title': 'Modificar Producto'
                    });

                    span = $('<span>', {'class': 'glyphicon glyphicon-edit'}).text(' Modificar');
                    _producto.append(span);
                    editar_producto.append(_producto);
                }

                $.each(data.costos, function(id, costo) {
                    if (costo.proveedor != undefined) {
                        provider    = $('<td>')
                            .text(costo.proveedor.nombre)
                            .append($('<input>', {
                                type: 'hidden',
                                value: costo.proveedor.id
                            }));
                    } else {
                        provider = $('<td>').text('-');
                    }

                    code = $('<td>').text(costo.codigo != undefined ? costo.codigo : '-');
                    cost = $('<td>', {'data-action':'#edit', 'data-content': id}).text(costo.costo);
                    select = $('<td>', {class: 'text-center', style: 'width: 1%;' })
                        .html('<a href="#cost" title="Seleccionar"><span class="fa fa-check"></span></a>');

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

                // This logic is implemented in line 298
                //if (data.uom != undefined && data.uom != null) {
                //    $('#' + lineas.form_id + '_uom').val(data.uom.id);
                //}

                lineas._update_importe_linea();
            });
        },
        _select_product_cost: function (event){
            event.preventDefault();

            var $this = $(this),
                costo = $this.parent().prev().text();

            if (costo != undefined && costo != null) {
                $('#' + lineas.form_id + '_precio_unitario').val(costo);
                $('#' + lineas.form_id + '_precio_unitario').trigger('change');
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

            var bruto = cantidadPedido * costoUnitario;
            var importeDescuento = bruto * porcientoDescuento / 100;

            if($impuesto.val() != undefined && $impuesto.val() != null && $impuesto.val() != '') {
                var tarifa  = $impuesto.find(':selected').data('tarifa'),
                    tipo    = $impuesto.find(':selected').data('tipo');
                if (tipo == 'porcentaje') {
                    importeImpuesto = bruto * tarifa / 100;
                } else {
                    importeImpuesto = tarifa;
                }
            }

            importeTotal = bruto + importeImpuesto - importeDescuento;
            if(!isNaN(importeTotal)) {
                $importeLinea.val(importeTotal);
            } else {
                $importeLinea.val(0);
            }

        }
    },

    producto = {
        element: 'select#buseta_bodegabundle_pedido_compra_linea_producto',

        findUOM: function () {
            var idproducto = $(producto.element).val();

            if (idproducto.length === 0 || isNaN(idproducto)) {
                uom.restore();
                return ;
            }

            $.ajax({
                url: Routing.generate('pedidocompra_get_uom_by_producto', {id: idproducto}),
                dataType: 'JSON',
                method: 'GET'
            }).done(function (response, textStatus, jqXHR) {
                uom.select(response.id);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 404) {
                    $btalerts.addWarning(jqXHR.responseJSON.error);
                } else if (jqXHR.status === 500) {
                    $btalerts.addWarning('Ha ocurrido un error inesperado.');
                }
            });
        },
        getElement: function() {
            return $(producto.element);
        }
    },

    uom = {
        element: 'select#buseta_bodegabundle_pedido_compra_linea_uom',

        restore: function () {
            $(uom.element).removeAttr('readonly');
            $(uom.element).find('option').removeAttr('disabled').removeAttr('selected');
            $(uom.element).find('option[value=""]').first().attr('selected','selected');
        },

        select: function (id) {
            $(uom.element).find('option[value="' + id + '"]').first()
                .attr('selected', 'selected')
                .removeAttr('disabled');
            $(uom.element).find('option[value!="' + id + '"]')
                .removeAttr('selected')
                .attr('disabled', 'disabled');

            if ($(uom.element).find('option[value="' + id + '"]').size() > 0) {
                uom.readonly();
            }
        },

        readonly: function () {
            $(uom.element).attr('readonly', 'readonly');
        }
    };

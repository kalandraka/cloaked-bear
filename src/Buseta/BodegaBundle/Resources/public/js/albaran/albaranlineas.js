var
    lineas = {
        form_name: '',
        form_id: '',
        id: '',
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
        _load: function (event) {

            if(event !== undefined) {
                event.preventDefault();
            }

            if (albaran.id == '') {
                return;
            }

            // add spinning to show loading process
            tabs._add_loadding('lineas');

            var url = Routing.generate('albaran_lineas_list',{'albaran': albaran.id});
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

            if(albaran.id === '' || albaran.id === undefined) {
                return;
            }

            var url = Routing.generate('albaran_lineas_new_modal', {'albaran': albaran.id});
            if($(this).attr('href') !== undefined && $(this).attr('href') === '#edit') {
                url = Routing.generate('albaran_lineas_edit_modal', {'albaran': albaran.id, id:$(this).data('content')});
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
            $('a#btn_lineas_save').off('click').on('click',  lineas._save_modal);

            $('a#btn_lineas_cancel').off('click').on('click', function(){
                $('div#form_lineas_modal').modal('hide');
            });

            $('#' + lineas.form_id + '_producto').off('change').on('change', function () {
                producto.findUOM();
                producto.hasSerial();
            });
            producto.findUOM();
            producto.hasSerial();

            // Chosen
            $('#' + lineas.form_id + '_producto').chosen({ alt_search: true });

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

            $('#' + lineas.form_id + '_almacen').chosen({ alt_search: true });
        },
        _load_delete_modal: function(event) {
            $('a[href="#delete"]').addClass('disabled');
            if(event !== undefined) {
                event.preventDefault();
            }

            if(albaran.id === '' || albaran.id === undefined) {
                return;
            }

            var id  = $(this).data('content'),
                url = Routing.generate('albaran_lineas_delete', {id: id});
            $.get(url)
                .done(function(response, textStatus, jqXHR){
                    $('div#form_albaranlinea_delete_modal').replaceWith($(response.view));

                    $('div#form_albaranlinea_delete_modal a#btn_albaranlinea_delete').on('click', lineas._save_delete_modal);
                    $('div#form_albaranlinea_delete_modal a#btn_albaranlinea_cancel').on('click', function(){
                        $('div#form_albaranlinea_delete_modal').modal('hide');
                    });

                    $('div#form_albaranlinea_delete_modal').modal('show');
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

            //Actualiza las nuevas lineas insertadas
            $('form#' + lineas.form_id).ajaxSubmit({
                success: lineas._done,
                error: utils._fail,
                complete: lineas._always,
                dataType: 'json'
            });
        },
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

            var deleteForm = $('div#form_albaranlinea_delete_modal').find('form'),
                url = $(deleteForm).attr('action');

            deleteForm.ajaxSubmit({
                success: function (response, textStatus, jqXHR) {
                    if(jqXHR.status == 202) {
                        $btalerts.addSuccess(response.message);
                    }
                    $('div#form_albaranlinea_delete_modal').modal('hide');

                    lineas._load();
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
        }
    },

    producto = {
        element: 'select#buseta_bodegabundle_albaran_linea_producto',

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
        hasSerial: function () {
            var idproducto = $(producto.element).val();

            if (idproducto.length === 0 || isNaN(idproducto)) {
                return ;
            }

            $.getJSON(Routing.generate('productos_has_serial', {id: idproducto}), function (json) {
                json.valor ? serial.show() : serial.hide();
            });
        },
        getElement: function() {
            return $(producto.element);
        }
    },

    uom = {
        element: 'select#buseta_bodegabundle_albaran_linea_uom',

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
    },

    serial = {
        element: 'textarea#buseta_bodegabundle_albaran_linea_seriales',

        show: function () {
            serial.getElement().parent().parent().show();
        },

        hide: function () {
            serial.getElement().parent().parent().hide();
        },
        getElement: function () {
            return $(serial.element);
        }
    };

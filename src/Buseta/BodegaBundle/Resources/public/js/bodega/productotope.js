/**
 * Created by root on 26/01/16.
 */
<!-- Productotope script -->
var productotope = {

        //productotope

        //form_name: '',
        form_id: '',
        id: '',

        //configuraciones
        config_form_nombre: 'productotope',

        config_form_div_listar: 'div#productotope',
        config_form_div_modal: 'div#form_productotope_modal',
        config_form_id_modal: '#form_productotope_modal',
        config_form_div_deletemodal: 'div#form_productotope_delete_modal',

        config_form_btn_save: 'a#btn_productotope_save',
        config_form_btn_cancel: 'a#btn_productotope_cancel',
        config_form_btn_delete: 'a#btn_productotope_delete',
        config_form_btn: 'btn_productotope_',

        config_ruta_listar: 'bodega_productotope_list',
        config_ruta_newmodal: 'productotope_new_modal',
        config_ruta_editmodal: 'productotope_edit_modal',
        config_ruta_deletemodal: 'productotope_delete_modal',

        config_pagn_lineaeliminada: false,
        config_pagn_lineasporpagina: 10,
        config_pagn_div_lineastotal: 'div#lineas_productotope_total',


//#form_productotope_modal


        _start_events: function () {
            //Cargar el modal
            $('a[href="' + productotope.config_form_id_modal + '"]').on('click', productotope._load_modal);
            // Paginator sort
            //$('table.productotope_records_list').find('a.sortable, a.asc, a.desc').on('click', productotope._load);
            // Table addresses actions
            $('table.productotope_records_list').find('a[href="#edit"]').on('click', productotope._load_modal);
            $('table.productotope_records_list').find('a[href="#delete"]').on('click', productotope._load_delete_modal);
            // Paginator navigation
            //aqui es para que la paginacion no se pierda!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!11
            //resolver el problema en inventario fisico
            $('div.paginator.row ul.pagination').find('a.paginator-link').on('click', productotope._load);
        },

        /**
         * Actualiza los eventos para el modal de lineas
         * @private
         */
        _linea_start_events: function () {
            $(productotope.config_form_btn_save).unbind('click');
            $(productotope.config_form_btn_save).on('click', productotope._save_modal);

            $(productotope.config_form_btn_cancel).unbind('click');
            $(productotope.config_form_btn_cancel).on('click', function () {
                $(productotope.config_form_div_modal).modal('hide');
            });

            // Chosen
            //Aqui si hay que establecer obligatoriamente
            $('#' + productotope.form_id + '_producto').chosen({alt_search: true});
            $('#' + productotope.form_id + '_almacen').chosen({alt_search: true});
        },
        _load: function (event) {

            if (event !== undefined) {
                event.preventDefault();
            }

            $(productotope.config_form_div_listar).unblock();

            //Obtenemos el id del reporte actualmente mostrado en el show
            /*config_nombre*/
            bodega.id = $('#bodega_id').val();

            if (bodega.id == '') {
                return;
            }

            // add spinning to show loading process
            tabs._add_loadding(productotope.config_form_nombre);

            var url = Routing.generate(productotope.config_ruta_listar, {'id': bodega.id});

            if ($(this).hasClass('sortable') || $(this).hasClass('desc') || $(this).hasClass('asc') || $(this).hasClass('paginator-link')) {
                url = $(this).attr('href');
            }
            else {
                var pagina_retorno = productotope._calcular_pagina_retorno();
                if (pagina_retorno)
                    //modifico la URL estableciendo la pagina a la cual retornar
                    url += '?page=' + pagina_retorno;
            }

            //reseteo la variable que indica que se elimino una linea en la ultima operacion.
            productotope.config_pagn_lineaeliminada = false;

            $.get(url).done(function (response, textStatus, jqXHR) {
                $(productotope.config_form_div_listar).html(response);
                productotope._start_events();
            }).fail(utils._fail).always(productotope._always);
        },
        //Resuelto problema de no mantener pagina actual en el editar y el eliminar cuando hay paginacion
        //hasta ahora regresaba a la primera pagina
        _calcular_pagina_retorno: function () {
                // si entra aqui no vengo de un enlace del paginador
                // que saltaba hacia la primera pagina siempre, ahora se mantiene en la
                // pagina mostrada actualmente .
                var nro_pagina_actual = $('div.paginator.row ul.pagination li.active span').text();
                if (nro_pagina_actual !== '' && nro_pagina_actual !== undefined && !isNaN(nro_pagina_actual)) {
                    /*posible modificacion del numero de la pagina a mostrar
                     *cuando estamos en la ultima pagina, queda una sola fila y esta se elimina
                     *o sea venimos de una eliminacion  */
                    //si alguna linea se elimino
                    if (productotope.config_pagn_lineaeliminada === true) {
                        var nro_lineas_total = parseInt($(productotope.config_pagn_div_lineastotal).text());
                        //si la pagina actual es la ultima pagina posible, y si en la pagina actual solo queda un registro
                        if ((parseInt(nro_lineas_total / productotope.config_pagn_lineasporpagina) == nro_pagina_actual - 1 )
                            && (nro_lineas_total % productotope.config_pagn_lineasporpagina) == 1) {
                            //entonces decremento en 1 la pagina actual
                            nro_pagina_actual--;
                        }
                    }
                    //modifico la URL estableciendo la pagina a la cual retornar
                    //url += '?page=' + nro_pagina_actual;
                } else nro_pagina_actual=false;

            return nro_pagina_actual;
        },
        _load_modal: function (event) {
            if (event !== undefined) {
                event.preventDefault();
            }

            if (bodega.id === '' || bodega.id === undefined) {
                return;
            }

            $(productotope.config_form_div_listar).block();

            var url = Routing.generate(productotope.config_ruta_newmodal, {'id': bodega.id});
            if ($(this).attr('href') !== undefined && $(this).attr('href') === '#edit') {
                url = Routing.generate(productotope.config_ruta_editmodal, {
                    'id': $(this).data('content'),
                    'bodega': bodega.id
                });

            }

            $.get(url)
                .done(function (response, textStatus, jqXHR) {
                    $(productotope.config_form_div_modal).replaceWith($(response.view));

                    // disable el select de almacen
                    //$('#buseta_bodegabundle_productotope_almacen').addClass('disabled');

                    productotope.form_id = $(productotope.config_form_div_modal).find('form').attr('id');
                    //productotope.form_name = $(productotope.config_form_div_modal).find('form').attr('name');

                    $(productotope.config_form_btn_save).on('click', productotope._save_modal);
                    $(productotope.config_form_btn_cancel).on('click', function () {
                        $(productotope.config_form_div_modal).modal('hide');
                    });

                    $(productotope.config_form_div_modal).modal('show');

                    productotope._linea_start_events();

                }).fail(utils._fail).always(productotope._always);
        }
        ,
        _load_delete_modal: function (event) {
            if (event !== undefined) {
                event.preventDefault();
            }

            if (bodega.id === '' || bodega.id === undefined) {
                return;
            }

            //$(productotope.config_form_div_listar).block();

            var id = $(this).data('content'),
                url = Routing.generate(productotope.config_ruta_deletemodal, {id: id});

            $.get(url)
                .done(function (response, textStatus, jqXHR) {
                    $(productotope.config_form_div_deletemodal).replaceWith($(response.view));
                    $(productotope.config_form_div_deletemodal + ' ' + productotope.config_form_btn_delete).on('click', productotope._save_delete_modal);
                    $(productotope.config_form_div_deletemodal + ' ' + productotope.config_form_btn_cancel).on('click', function () {
                        $(productotope.config_form_div_deletemodal).modal('hide');
                    });
                    $(productotope.config_form_div_deletemodal).modal('show');
                }).fail(utils._fail).always(function () {
                });
        }
        ,
        _save_modal: function (event) {
            if (event != undefined) {
                event.preventDefault();
            }

            button._disable(productotope.config_form_btn_save);

            $(productotope.config_form_btn_save).find('span')
                .removeClass('glyphicon')
                .removeClass('glyphicon-save')
                .addClass('fa')
                .addClass('fa-gear')
                .addClass('fa-spin');

            //Actualiza las nuevas lineas insertadas
            $('form#' + productotope.form_id).ajaxSubmit({
                success: productotope._done,
                error: utils._fail,
                complete: productotope._always,
                dataType: 'json'
            });
        }
        ,
        _save_delete_modal: function (event) {
            if (event != undefined) {
                event.preventDefault();
            }

            $(productotope.config_form_btn_delete).find('span')
                .removeClass('glyphicon')
                .removeClass('glyphicon-save')
                .addClass('fa')
                .addClass('fa-gear')
                .addClass('fa-spin');

            var deleteForm = $(productotope.config_form_div_deletemodal).find('form'),
                url = $(deleteForm).attr('action');

            deleteForm.ajaxSubmit({
                success: function (response, textStatus, jqXHR) {
                    if (jqXHR.status == 202) {
                        $btalerts.addSuccess(response.message);
                    }
                    $(productotope.config_form_div_deletemodal).modal('hide');

                    //indico que se elimino una linea en la ultima operacion.
                    productotope.config_pagn_lineaeliminada = true;

                    productotope._load();
                },
                error: utils._fail,
                complete: productotope._always,
                url: url,
                dataType: 'json'
            });
        }
        ,
        _done: function (response, textStatus, jqXHR) {
            $('form#' + productotope.form_id).replaceWith($(response.view).find('form'));
            if (jqXHR.status == 201 || jqXHR.status == 202) {
                $btalerts.addSuccess(response.message);
                $(productotope.config_form_div_modal).modal('hide');
                productotope._load();
            } else {
                productotope._linea_start_events();
            }
        }
        ,
        _always: function (jqXHR, textStatus) {
            // remove spinning
            tabs._remove_loadding(productotope.config_form_nombre);
            //$('a[id^="btn_productotope_"]').find('span')
            // cambio en el boton de salvar
            $('a[id^="' + productotope.config_form_btn + '"]').find('span')
                .addClass('glyphicon')
                .addClass('glyphicon-save')
                .removeClass('fa')
                .removeClass('fa-gear')
                .removeClass('fa-spin');

            button._enable(productotope.config_form_btn_save);
            $(productotope.config_form_div_listar).unblock();

        }

    }
    ;

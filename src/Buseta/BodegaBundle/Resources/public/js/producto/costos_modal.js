var costos = {
    form_name: '',
    form_id: '',
    id: '',
    product_id: '',
    _after_changes: function () {
    },
    _start_events: function () {
        $('a[href="#form_costos_modal"]').on('click', costos._load_modal);
        // Paginator sort
        $('table.costos_records_list').find('a.sortable, a.asc, a.desc').on('click', costos._load);
        // Table addresses actions
        $('table.costos_records_list').find('a[href="#edit"]').on('click', costos._load_modal);
        $('table.costos_records_list').find('a[href="#delete"]').on('click', costos._load_delete_modal);
        // Paginator navigation
        $('div.costos-paginator.row ul.pagination').find('a.paginator-link').on('click', costos._load)
    },
    _load: function (product_id, after_changes) {
        if (after_changes)
            costos._after_changes = after_changes;
        if (product_id == '') {
            if (costos.product_id != '')
                product_id = costos.product_id;
            else
                return;
        }
        else {
            costos.product_id = product_id;
        }

        var url = Routing.generate('producto_costos_modal_list', {'producto': product_id});
        $.get(url)
            .done(function (response, textStatus, jqXHR) {
                $('div#form_costos_list_modal').modal('hide');
                $('div#form_costos_list_modal').replaceWith($(response.view));
                $('div#form_costos_list_modal').modal('show');
                costos._after_changes();
                costos._start_events();
            }).fail(utils._fail).always(function () {
        });
    },
    _load_modal: function (event) {
        if (event !== undefined) {
            event.preventDefault();
        }

        if (costos.product_id === '' || costos.product_id === undefined) {
            return;
        }

        var url = Routing.generate('producto_costos_new_modal', {'producto': costos.product_id});
        if ($(this).attr('href') !== undefined && $(this).attr('href') == '#edit') {
            url = Routing.generate('producto_costos_edit_modal', {
                'producto': costos.product_id,
                id: $(this).data('content')
            });
        }

        $.get(url)
            .done(function (response, textStatus, jqXHR) {
                $('div#form_costos_modal').replaceWith($(response.view));

                costos.form_id = $('div#form_costos_modal').find('form').attr('id');
                costos.form_name = $('div#form_costos_modal').find('form').attr('name');
                $('select#' + costos.form_id + '_proveedor').chosen();
                $('a#btn_costos_save').on('click', costos._save_modal);
                $('a#btn_costos_cancel').on('click', function () {
                    $('div#form_costos_modal').modal('hide');
                });

                $('div#form_costos_modal').modal('show');
            }).fail(utils._fail).always(function () {
        });
    },
    _load_delete_modal: function (event) {
        if (event !== undefined) {
            event.preventDefault();
        }

        if (costos.product_id === '' || costos.product_id === undefined) {
            return;
        }

        var id = $(this).data('content'),
            url = Routing.generate('producto_costo_delete', {id: id});
        $.get(url)
            .done(function (response, textStatus, jqXHR) {
                $('div#form_costos_delete_modal').replaceWith($(response.view));

                $('div#form_costos_delete_modal a#btn_costos_delete').on('click', costos._save_delete_modal);
                $('div#form_costos_delete_modal a#btn_costos_cancel').on('click', function () {
                    $('div#form_costos_delete_modal').modal('hide');
                });

                $('div#form_costos_delete_modal').modal('show');
            }).fail(utils._fail).always(function () {
        });
    },
    _save_modal: function (event) {

        if (event != undefined) {
            event.preventDefault();
        }

        $('#btn_costos_save').find('span')
            .removeClass('glyphicon')
            .removeClass('glyphicon-save')
            .addClass('fa')
            .addClass('fa-gear')
            .addClass('fa-spin');

        $('form#' + costos.form_id).ajaxSubmit({
            success: costos._done,
            error: utils._fail,
            complete: costos._always,
            dataType: 'json'
        });
    },
    _save_delete_modal: function (event) {
        if (event != undefined) {
            event.preventDefault();
        }

        $('#btn_costos_delete').find('span')
            .removeClass('glyphicon')
            .removeClass('glyphicon-trash')
            .addClass('fa')
            .addClass('fa-gear')
            .addClass('fa-spin');

        var deleteForm = $('div#form_costos_delete_modal').find('form'),
            url = $(deleteForm).attr('action');

        deleteForm.ajaxSubmit({
            success: function (response, textStatus, jqXHR) {
                $('div#form_costos_delete_modal').modal('hide');
                costos._load(costos.product_id, costos._after_changes);
            },
            error: utils._fail,
            complete: costos._always,
            url: url,
            dataType: 'json'
        });
    },
    _done: function (response, textStatus, jqXHR) {
        $('form#' + costos.form_id).replaceWith($(response.view).find('form'));

        $('select#' + costos.form_id + '_proveedor').chosen();

        if (jqXHR.status == 201 || jqXHR.status == 202) {
            $('div#form_costos_modal').modal('hide');
            costos._load(costos.product_id, costos._after_changes);
        }
    },
    _always: function (jqXHR, textStatus) {
        // remove spinning
        tabs._remove_loadding('costos');
        $('a[id^="btn_costos_"]').find('span')
            .addClass('glyphicon')
            .addClass('glyphicon-save')
            .removeClass('fa')
            .removeClass('fa-gear')
            .removeClass('fa-spin');
    }
};

var progressBar = {
    _add_progressBar: function (name) {
        var progressBar = $('<div class="progress" id="' + name + '_progress_bar"></div>'),
            bar = $('<div>')
                .addClass('progress-bar')
                .attr('role', 'progressbar')
                .attr('aria-valuenow', 2)
                .attr('aria-valuemin', 0)
                .attr('aria-valuemax', 100)
                .css('width', '2%')
                .append($('<span class="sr-only"></span>'));

        progressBar.append(bar);

        $('div#' + name).prepend(progressBar);
    },
    _remove_progressBar: function(name) {
        var progressBar = $('div#' + name + '_progress_bar');
        progressBar.slideUp(400, function () {
            progressBar.remove();
        });
    }
};
var archivoadjunto = {
    collectionHolder: $('#' + autobus.form_id + '_archivosAdjuntos'),
    index: 0,
    prototype: '',
    _load: function () {
        archivoadjunto.index     = archivoadjunto.collectionHolder.find('a.delete_archivo_link').length;
        archivoadjunto.prototype = archivoadjunto.collectionHolder.data('prototype');

        $.ajax({
            url: Routing.generate('busetabuses_autobus_archivoadjunto', {id: autobus.id}),
            dataType: 'JSON',
            method: 'GET'
        }).done(function (json, status, XHR) {
            //console.log(status);
            if (XHR.status == 200) {
                $('div#archivosadjuntos').html(json.view);
                // reectificando css en formulario
                $('div#archivosadjuntos').find('form div.form-group').css('margin-bottom',0);
                archivoadjunto._start_events();
            }
        });
    },
    _start_events: function () {
        $('a.delete_archivo_link')
            .unbind('click')
            .on('click', archivoadjunto.remove);
        $('a[href="#addArchivo"].btn.btn-default')
            .unbind('click')
            .on('click', function (e) {
                e.preventDefault();

                var form = $('div#archivosadjuntos').find('form'),
                    file = form.find('input[type="file"]');

                file.trigger('click');
                file.unbind('change')
                    .on('change', archivoadjunto.add)
            });
    },
    add: function (event) {
        event.preventDefault();

        var inputFile = $(this),
            $form = inputFile.closest('form');

        progressBar._add_progressBar('archivosadjuntos');
        $('a[href="#addArchivo"]').hide();

        $form.ajaxSubmit({
            dataType: 'JSON',
            uploadProgress: archivoadjunto._uploadProgress,
            success: function (json, statusText, jqXHR) {
                if (jqXHR.status === 201) {
                    // success message
                    archivoadjunto._load();
                } else {
                    $('div#archivosadjuntos').html(json.view);
                    archivoadjunto._start_events();
                }
            },
            error: function (json) {
                console.error(json.message);
                $('div#archivosadjuntos').html(json.view);
            },
            complete: function (){
                progressBar._remove_progressBar('archivosadjuntos');
                $('a[href="#addArchivo"]').show();
            }
        });
    },
    updateView: function () {
        if(archivoadjunto.index === undefined || archivoadjunto.index === 0){
            $('div#no-elements-tr').show();
        }else {
            $('div#no-elements-tr').hide();
            archivoadjunto._start_events();
        }
    },
    remove: function (event) {
        var idArchivoAdjunto = $(this).data('id'),
            parent = $(this).parent().parent(),
            modal = $('div#modal-delete-archivoadjunto');

        if (idArchivoAdjunto !== undefined) {
            modal.find('.btn.btn-danger')
                .unbind()
                .on('click', {remove: true, archivo: idArchivoAdjunto, parent: parent}, archivoadjunto.remove);
            modal.find('div.modal-body strong').eq(0).html(parent.find('div.well.well-small').html());
            $('div#modal-delete-archivoadjunto').modal('show');
        } else if (event.data !== undefined && event.data.remove === true) {
            $.ajax({
                url: Routing.generate('busetabuses_autobus_archivoadjunto_delete', {'id': autobus.id, archivo: event.data.archivo}),
                dataType: 'JSON',
                method: 'DELETE'
            }).done(function (json) {
                //console.log(json.message);
                archivoadjunto._load();
            }).fail(function (error) {
                console.error(error.message);
            }).always(function () {
                modal.modal('hide');
            });
        } else {
            parent.remove();
        }
    },
    _uploadProgress: function (event, position, total, percentComplete) {
        var progressBarr = $('div#archivosadjuntos').find('.progress-bar')[0];
        if (progressBarr !== undefined) {
            $(progressBarr).css('width', percentComplete + '%');
            $(progressBarr).find('span').html(percentComplete + '% Completado');
        }
    }
};

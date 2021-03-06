var
    button = {
        _disable: function (selector) {
            $(selector).addClass('disabled');
        },
        _enable: function (selector) {
            $(selector).removeClass('disabled');
        }
    },
    tabs = {
        loadding_image: '<i class="fa fa-spinner fa-spin"></i>',
        _show_all_tabs: function () {
            tabs._show_tab('lineas');
        },
        _show_tab: function (tabname) {
            $('li a[href="#' + tabname + '"]').parent().show();
        },
        _hide_tab: function () {
            $('li a[href="#' + tabname + '"]').parent().hide();
        },
        _add_loadding: function (tabname) {
            var linktab = $('li a[href="#' + tabname + '"]');
            linktab.prepend($(tabs.loadding_image));
        },
        _remove_loadding: function (tabname) {
            var spinning = $('li a[href="#' + tabname + '"]').find('i.fa.fa-spinner.fa-spin');
            spinning.remove();
        },
        _active: function (href) {
            $('li a[href="' + href + '"]').parent().addClass('active');
            $('div' + href).addClass('active').addClass('in');
        },
        _desactive: function (href) {
            $('li a[href="' + href + '"]').parent().removeClass('active');
            $('div' + href).removeClass('active').removeClass('in');
        }
    },
    utils = {
        _fail: function (jqXHR, textStatus, errorThrown) {
            if(jqXHR.status == 500 && jqXHR.responseText.message != undefined) {
                $btalerts.addDanger(jqXHR.responseText.message);
            } else {
                $btalerts.addDanger('Ha ocurrido un error inesperado.');
            }
        }
    },
    progressBar = {
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

            $('#' + name).hide().parent().prepend(progressBar);
        },
        _remove_progressBar: function (name) {
            var progressBar = $('div#' + name + '_progress_bar');
            progressBar.slideUp(400, function () {
                progressBar.remove();
            });
            $('#' + name).show();
        }
    };

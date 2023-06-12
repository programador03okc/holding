$(document).ready(function () {
    obtenerLista();

    $('#formulario').on('submit', function () {
        var data = $(this).serializeArray();
        $.ajax({
            type: "POST",
            url : route('mgcp.acuerdo-marco-componentes-tipo.registrar'),
            data: data,
            dataType: "JSON",
            success: function (response) {
                if (response.response == 'ok') {
                    obtenerLista();
                }
                Util.notify(response.alert, response.message);
            }
        }).fail( function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        });
        
        return false;
    });
});

function obtenerLista() {
    var $tabla = $('#tablaComponente').DataTable({
        dom: 'frtip',
        pageLength: 20,
        language: idioma,
        serverSide: true,
        initComplete: function (settings, json) {
            const $filter = $('#tablaComponente_filter');
            const $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fas fa-search"></i></button>');
            $input.off();
            $input.on('keyup', (e) => {
                if (e.key == 'Enter') {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').on('click', (e) => {
                $tabla.search($input.val()).draw();
            });
        },
        drawCallback: function (settings) {
            $('#tablaComponente_filter input').prop('disabled', false);
            $('#btnBuscar').html('<i class="fas fa-search"></i>').prop('disabled', false);
            $('#tablaComponente_filter input').trigger('focus');
        },
        order: [[2, 'asc']],
        ajax: {
            url: route('recursos_humanos.escalafon.persona.listar'),
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrf_token}
        },
        columns: [
            {
                render: function (data, type, row, index) {
                    return index.row + 1;
                }, orderable: false, searchable: false, className: 'text-center'
            },
            {data: 'descripcion'},
            {data: 'accion', orderable: false, searchable: false, className: 'text-center'}
        ]
    });
    $tabla.on('search.dt', function() {
        $('#tablaComponente_filter input').attr('disabled', true);
        $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
    });
    $tabla.on('init.dt', function(e, settings, processing) {
        $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
    });
    $tabla.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
        } else {
            $(e.currentTarget).LoadingOverlay("hide", true);
        }
    });
}
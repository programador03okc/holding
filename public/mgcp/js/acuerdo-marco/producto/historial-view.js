class HistorialProductoView {

    constructor(model) {
        this.model = model;
    }

    obtenerHistorialEvent = () => {
        $('tbody').on("click", ".historial-actualizaciones", (e) => {
            e.preventDefault();
            const $tbody = $('#tbodyHistorial');
            const $elemento = $(e.currentTarget);
            const $modal = $('#modalHistorialActualizaciones');
            const $mensaje = $modal.find('div.mensaje');
            Util.bloquearConSpinner($mensaje);
            $modal.find('span.producto').html($elemento.data('marca') + ' ' + $elemento.data('modelo') + ' ' + $elemento.data('nroparte'));
            $tbody.html('');
            this.model.obtenerHistorialActualizaciones($elemento.data('producto'), $elemento.data('empresa')).then((datos) => {
                let cadena = '';
                if (datos.length == 0) {
                    cadena = '<tr><td colspan="5" class="text-center">Sin historial de actualizaciones</td></tr>';
                }
                for (let indice in datos) {
                    cadena += `<tr>
                    <td class="text-center">${datos[indice].usuario.name}</td>
                    <td class="text-center">${datos[indice].empresa.empresa}</td>
                    <td>${datos[indice].detalle}</td>
                    <td>${datos[indice].comentario}</td>
                    <td class="text-center">${datos[indice].fecha}</td>
                    </tr>`;
                }
                $tbody.html(cadena);
                Util.liberarBloqueoSpinner($mensaje);
            }).fail(() => {
                alert('Hubo un problema al obtener datos. Por favor actualice la página y vuelva a intentarlo');
                $modal.modal('hide');
            });
        });
    }

    listar = () => {
        const model = this.model;
        const $tableHistorial = $('#tableHistorial').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableHistorial_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').click(function () {
                    $tableHistorial.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableHistorial_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableHistorial_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[6, "desc"]],
            ajax: {
                url: route('mgcp.acuerdo-marco.productos.historial-actualizaciones.data-lista'),
                type: "POST",
                data: { _token: model.token },
            },
            columns: [
                { data: 'name', name: 'users.name' },
                { data: 'marca', name: 'productos_am.marca' },
                { data: 'modelo', name: 'productos_am.modelo' },
                { data: 'part_no', name: 'productos_am.part_no' },
                { data: 'detalle' },
                { data: 'comentario' },
                { data: 'fecha', searchable: false, className: 'text-center' },
                { data: 'id', searchable: false, className: 'text-center', orderable: false }
            ],
            columnDefs: [

                {
                    render: function (data, type, row) {
                        let botones = `
                        <div class="btn-group" role="group">
                            <a title="Ver foto" target="_blank" href="${row.imagen}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></button>
                            <a title="Descargar ficha técnica" target="_blank" href="${row.ficha_tecnica}" class="btn btn-default btn-xs"><span class="fa fa-file-pdf-o" aria-hidden="true"></span></a>
                            <br>
                            <button title="Actualizar stock de producto en Perú Compras" data-toggle="modal" data-target="#modalActualizarDataPortal" data-marca="${row.marca}" data-modelo="${row.modelo}" data-nroparte="${row.part_no}" data-tipo="stock" data-producto="${row.id}" class="btn btn-default btn-xs actualizar-stock-precio"><span class="fa fa-cube" aria-hidden="true"></span></button>
                            <button title="Actualizar precio de producto en Perú Compras" data-toggle="modal" data-target="#modalActualizarDataPortal" data-marca="${row.marca}" data-modelo="${row.modelo}" data-nroparte="${row.part_no}" data-tipo="precio" data-producto="${row.id}" class="btn btn-default btn-xs actualizar-stock-precio"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></button>
                        </div>`;
                        return botones;
                    }, targets: 7
                }
            ],
            buttons: []
        });

        $tableHistorial.on('search.dt', function () {
            $('#tableHistorial_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

        $tableHistorial.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });
    }
}
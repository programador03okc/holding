class NotificacionAmView {
    constructor(model) {
        this.model = model;
    }

    listar = () => {
        const model = this.model;
        const $tableDatos = $('#tableDatos').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableDatos_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableDatos.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableDatos_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#tableDatos_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return row.acuerdo_marco + ' - ' + row.descripcion_corta;
                    }, targets: 2
                },
                {
                    render: function (data, type, row) {
                        return `<button title="Historial de notificación" data-id="${row.id}" class="historial-notificacion btn btn-xs btn-${row.estado == 'LEIDO' ? 'success' : 'warning'}">${row.estado}</button>`;
                    }, targets: 6
                },
                {
                    render: function (data, type, row) {
                        return `<button class="btn btn-primary btn-sm ver-notificacion" data-id="${row.id}">Ver</button>`;
                    }, targets: 8
                },
            ],
            order: [
                [5, "desc"]
            ],
            ajax: {
                url: route('mgcp.acuerdo-marco.notificaciones.data-lista'),
                type: "POST",
                data: {
                    _token: model.token
                }

            }, columns: [
                { data: 'emitido_por', name: 'emitido.nombre' },
                { data: 'destinatario', name: 'destino.nombre' },
                { data: 'acuerdo_marco', name: 'acuerdo_marco.descripcion' },
                { data: 'orden_compra', className: ' text-center' },
                { data: 'asunto' },
                { data: 'fecha', className: ' text-center', searchable: false },
                { data: 'estado', className: ' text-center' },
                { data: 'plazo', className: ' text-center', searchable: false },
                { data: 'descripcion_corta', name: 'acuerdo_marco.descripcion_corta', className: ' text-center', orderable: false }, //Botones
            ],
            buttons: [{
                text: '<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Actualizar lista...',
                action: function () {
                    $('#modalActualizarLista').modal('show');
                }, className: 'btn-sm'
            }],
        });

        $tableDatos.on('search.dt', function () {
            $('#tableDatos_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

        $tableDatos.on('processing.dt', function (e, settings, processing) {
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

    historialNotificacionEvent = () => {
        $('#tableDatos').on('click', 'button.historial-notificacion', (e) => {
            const $modal = $('#modalHistorialNotificacion');
            Util.bloquearConSpinner($modal.find('div.mensaje'));
            $modal.modal('show');
            $('#tableHistorialNotificacion').find('tbody').html('');
            this.model.obtenerHistorialNotificacion($(e.currentTarget).data('id')).then((resultado) => {
                if (resultado.tipo == 'success') {
                    let filas = '';
                    for (let indice in resultado.data) {
                        filas += `<tr>
                        <td class="text-center">${resultado.data[indice].estado}</td>
                        <td class="text-center">${resultado.data[indice].fecha}</td>
                        <td class="text-center">${resultado.data[indice].usuario}</td>
                        </tr>`;
                    }
                    $('#tableHistorialNotificacion').find('tbody').html(filas);
                }
                else {
                    alert("No se pudo obtener el historial. Por favor actualice la página e intente de nuevo");
                    $modal.modal('hide');
                }
            }).fail(() => {
                alert("No se pudo obtener el historial. Por favor actualice la página e intente de nuevo");
                $modal.modal('hide');
            }).always(() => {
                Util.liberarBloqueoSpinner($modal.find('div.mensaje'));
            });
        });
    };

    verNotificacionEvent = () => {
        $('#tableDatos').on('click', 'button.ver-notificacion', (e) => {
            const $modal = $('#modalVerNotificacion');
            Util.bloquearConSpinner($modal.find('div.mensaje-modal'));
            $modal.find('.data').html('');
            $modal.modal('show');
            this.model.obtenerDetallesNotificacion($(e.currentTarget).data('id')).then((data) => {
                if (data.tipo == 'success') {
                    $modal.find('h4.titulo').html(data.notificacion.titulo);
                    $modal.find('div.acuerdo-marco').html(data.notificacion.acuerdo_marco);
                    $modal.find('div.orden-compra').html(data.notificacion.orden_compra);
                    $modal.find('label.tipo-entidad-1').html(data.notificacion.tipo_entidad_1);
                    $modal.find('div.entidad-1').html(data.notificacion.entidad_1);
                    $modal.find('div.subtitulo').html(data.notificacion.subtitulo);
                    $modal.find('div.fecha-hora').html(data.notificacion.fecha_envio);
                    $modal.find('label.tipo-entidad-2').html(data.notificacion.tipo_entidad_2);
                    $modal.find('div.entidad-2').html(data.notificacion.entidad_2);
                    $modal.find('div.asunto').html(data.notificacion.asunto);
                    $modal.find('div.mensaje').html(data.notificacion.mensaje);
                    $modal.find('div.denominacion-documento').html(data.notificacion.denominacion_documento);
                    $modal.find('div.documento-adjunto').html(`<a target="_blank" href="${data.notificacion.enlace}">Descargar</a>`);
                    if (data.actualizar) {
                        $(e.currentTarget).closest('tr').find('button.historial-notificacion').removeClass('btn-warning').addClass('btn-success').html('LEIDO');
                    }
                }
            }).fail(() => {
                alert('Hubo un problema al obtener el detalle de la notificación. Por favor actualice la página e intente de nuevo');
                $modal.modal('hide');
            }).always(() => {
                Util.liberarBloqueoSpinner($modal.find('div.mensaje-modal'));
            });
        });
    }

    obtenerFechasDescargaEvent = () => {
        $('#modalActualizarLista').on('show.bs.modal', (e) => {
            const $mensaje = $('#modalActualizarLista').find('div.mensaje');
            Util.bloquearConSpinner($mensaje);
            //$('#tableFechasDescarga').find('tbody').html('');
            this.model.obtenerFechasDescarga().then((data) => {
                $mensaje.html(data);
                //$('#tableFechasDescarga').find('tbody').html(data);
            }).fail(() => {
                alert('Hubo un problema al obtener las fechas. Por favor actualice la página e intente de nuevo');
                $('#modalActualizarLista').modal('hide');
            }).always(() => {
                Util.liberarBloqueoSpinner($mensaje);
            });
        })
    }

    actualizarListaEvent = () => {
        $('#btnActualizarLista').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Actualizando');
            this.model.actualizarLista().then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#tableDatos').DataTable().ajax.reload();
                    $('#modalActualizarLista').modal('hide');
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al actualizar. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Actualizar');
            });
        })
    }
}
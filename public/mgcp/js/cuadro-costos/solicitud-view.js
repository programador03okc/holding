class SolicitudView {
    constructor(id, model) {
        this.model = model;
        this.id = id;
        this.enviarEvent();
        this.responderEvent();
        this.listarEvent();
    }

    enviarEvent = () => {

        $('#btnSolicitarRetiroAprobacion').on('click', (e) => {
            const $modal = $('#modalEnviarSolicitud');
            $modal.find('h4.modal-title').html('Solicitar retiro de aprobación');
            $modal.modal('show');
        });

        $('#btnSolicitarReapertura').on('click', (e) => {
            const $modal = $('#modalEnviarSolicitud');
            $modal.find('h4.modal-title').html('Solicitar reapertura de cuadro');
            $modal.modal('show');
        });

        $('#btnSolicitarAprobacion').on('click', (e) => {
            let aprobacion_prev = $("[name=nueva_aprobacion]").val();

            if (aprobacion_prev == 0) {
                this.solicitarAprobacionEvento();
            } else {
                //Consultar en la BD las aprobaciones
                this.model.consultaSolicitudPrevia(this.id).then((respuesta) => {
                    if (respuesta == 1) {
                        this.solicitarAprobacionEvento();
                    } else {
                        Swal.fire({ icon: "info", title: "Aprobación previa seleccionada", text: "Tiene una aprobación pendiente, revisar la Información Adicional", });
                    }
                });
            }
        });

        //Enviar solicitud para aprobación, retiro o reapertura
        $('div.modal button.enviar-solicitud').on('click', (e) => {
            $(e.currentTarget).closest('div').find('button').prop('disabled', true);
            $(e.currentTarget).html(Util.generarPuntosSvg() + 'Enviando');
            this.model.enviar($(e.currentTarget).closest('div.modal').find('form').serialize()).then((data) => {
                Swal.fire({ icon: data.tipo, title: data.titulo, text: data.texto, }).then((result) => {
                    if (data.tipo=='success') {
                        location.reload();
                    }
                });
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al enviar su solicitud. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $(e.currentTarget).html('Enviar');
                $(e.currentTarget).closest('div').find('button').prop('disabled', false);
            });
        });

        $('#nueva_aprobacion').on('change', (e) => {
            this.model.solicitudPrevia(this.id, e.currentTarget.value).then((respuesta) => {
                Swal.fire({ icon: respuesta.tipo, title: respuesta.titulo, text: respuesta.texto, }).then((result) => {
                    if (respuesta.tipo == 'success') {
                        location.reload();
                    }
                });
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al enviar su solicitud. Por favor actualice la página e intente de nuevo');
            });
        });
    }

    solicitarAprobacionEvento = () => {
        Util.bloquearConSpinner($('#modalVistaPreviaCuadro').find('div.mensaje-inicial'));
        $('#modalVistaPreviaCuadro').modal('show');
        this.model.obtenerDetallesFilas(this.id).then((respuesta) => {
            let contenedor = respuesta.data.cuadroAm;
            let filas = '';
            for (let indice in contenedor.filas) {
                filas += `<tr>
                <td>${contenedor.filas[indice].part_no}</td>
                <td>${contenedor.filas[indice].descripcion}</td>
                <td class="text-center">${contenedor.filas[indice].cantidad}</td>
                <td>${contenedor.filas[indice].proveedor.nombre == null ? '' : contenedor.filas[indice].proveedor.nombre}</td>
                <td class="text-right">${contenedor.filas[indice].costo_compra_mas_flete_format}</td>
                <td class="text-right">${contenedor.filas[indice].ganancia_format}</td>
                </tr>`;
            }
            $('#tableVistaPrevia tbody').html(filas);
            const $footer = $('#tableVistaPrevia tfoot');
            $footer.find('td.bienes-servicio').html('-' + respuesta.data.cuadroBs.suma_costo_compra_mas_flete_format);
            $footer.find('td.gastos-generales').html('-' + respuesta.data.cuadroGg.suma_total_format);
            $footer.find('td.ganancia-real').html(`<strong>${contenedor.ganancia_real_format}</strong>`);
            $footer.find('td.margen-ganancia').html(`<strong>${contenedor.margen_ganancia_format}</strong>`);
            $footer.find('td.condicion-credito').html(respuesta.data.condicion_credito);
        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Problema al procesar su solicitud',
                text: 'Por favor actualice la página e intente de nuevo'
            })
            $('#modalVistaPreviaCuadro').modal('hide');
        }).always(() => {
            Util.liberarBloqueoSpinner($('#modalVistaPreviaCuadro').find('div.mensaje-inicial'));
        });
    }

    responderEvent = () => {
        $('#btnResponderSolicitud').on('click', () => {
            const $modal = $('#modalResponderSolicitud');
            const $aprobar = $('#selectResponderAprobar');
            const $comentario = $('#txtResponderComentario');
            $modal.find('button').prop('disabled', true);
            $('#btnResponderSolicitud').html(Util.generarPuntosSvg() + 'Enviando');
            this.model.responder(this.id, $aprobar.val(), $comentario.val()).then((data) => {
                Swal.fire({
                    icon: data.tipo,
                    title: data.titulo,
                    text: data.texto,
                }).then((result) => {
                    if (data.tipo=='success') {
                        location.reload();
                    }
                })
            }).fail(() => {
                Util.notify("error", "Hubo un problema al responder la solicitud. Por favor actualice la página e intente de nuevo");
            }).always(() => {
                $modal.find('button').prop('disabled', false);
                $('#btnResponderSolicitud').html('Enviar');
                $aprobar.prop('disabled', false);
                $comentario.prop('disabled', false);
            });
        });
    }

    listarEvent = () => {
        $('#btnListarSolicitudes').on('click', () => {
            Util.bloquearConSpinner($('#modalSolicitudes').find('div.mensaje-inicial'));
            const $tbody = $('#tableSolicitudes').find('tbody');
            $tbody.html('');
            this.model.listar(this.id).then((data) => {
                let filas = '';
                for (let indice in data) {
                    filas += `<tr>
                    <td class="text-center">${data[indice].fecha_solicitud}</td>
                    <td>${data[indice].tiposolicitud.tipo}</td>
                    <td>${data[indice].enviada_por.name}</td>
                    <td>${data[indice].comentario_solicitante}</td>
                    <td>${data[indice].enviada_a.name}</td>
                    <td class="text-center">${data[indice].aprobacion}</td>
                    <td>${data[indice].comentario_aprobador}</td>
                    <td class="text-center">${data[indice].fecha_respuesta}</td>`;
                    filas += '</tr>';
                }
                $tbody.html(filas);

            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al procesar su solicitud',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
                $('#modalSolicitudes').modal('hide');
            }).always(() => {
                Util.liberarBloqueoSpinner($('#modalSolicitudes').find('div.mensaje-inicial'));
            });
        });
    }
}
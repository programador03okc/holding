class EntidadView {
    constructor(model) {
        this.model = model;
    }

    listar = (puedeEditar) => {
        const model = this.model;
        const $tableEntidades = $('#tableEntidades').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableEntidades_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableEntidades.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableEntidades_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableEntidades_filter input').focus();
                $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[0, "asc"]],
            ajax: {
                url: route('mgcp.acuerdo-marco.entidades.data-lista'),
                type: "POST",
                data: { _token: model.token },

            },
            columns: [
                { data: 'ruc', className: 'text-center' },
                { data: 'nombre' },
                { data: 'direccion' },
                { data: 'ubigeo' },
                { data: 'responsable' },
                { data: 'cargo' },
                { data: 'telefono', className: 'text-center', searchable: false },
                { data: 'correo', searchable: false },
            ],
            columnDefs: [
                { orderable: false, targets: [8] },
                { className: "text-center", targets: [8] },
                {
                    render: function (data, type, row) {
                        return row.semaforo + ' ' + row.nombre;
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        let botones = '';
                        /*if (puedeEditar) {
                          botones = `<button title="Editar entidad" data-toggle="modal" data-target="#modalEditarEntidad" data-id="${row.id}" class="btn btn-primary btn-xs entidad"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>`;
                        }*/
                        return botones;
                    }, targets: 8
                }
            ],
            buttons: [
            ]
        });

        $tableEntidades.on('search.dt', function () {
            $('#tableEntidades_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

        $tableEntidades.on('processing.dt', function (e, settings, processing) {
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

    obtenerDetallesEntidad = (e) => {
        e.preventDefault();
        const $modal = $('#modalEntidad');
        const $contenedor = $('#formEntidad');
        $modal.modal('show');
        const $mensajeInicial = $modal.find('div.mensaje-inicial');
        const $mensajeFinal = $modal.find('div.mensaje-final');
        $mensajeFinal.html('');
        Util.bloquearConSpinner($mensajeInicial);
        $contenedor.find('div.form-control-static').html('');
        this.model.obtenerDetalles($(e.currentTarget).data('id')).then((data) => {
            $contenedor.find('input[name=id]').val(data.id);
            $contenedor.find('.ruc').html(data.ruc);
            $contenedor.find('.nombre').html(data.nombre);
            $contenedor.find('.direccion').html(data.direccion);
            $contenedor.find('.ubigeo').html(data.ubigeo);
            $contenedor.find('.responsable').html(data.responsable);
            $contenedor.find('.telefono').html(data.telefono);
            $contenedor.find('.cargo').html(data.cargo);
            $contenedor.find('.correo').html(data.correo);

            $contenedor.find('.semaforo').html(data.semaforo)
            Util.liberarBloqueoSpinner($mensajeInicial);
            //$contenedor.fadeIn(300);
        }).fail(() => {
            alert("Hubo un problema al obtener los detalles de la entidad. Por favor actualice la página e intente de nuevo");
            $modal.modal('hide');
        });
    }

    obtenerDetallesEvent = () => {
        $('body').on('click', 'a.entidad', (e) => {
            e.preventDefault();
            this.obtenerDetallesEntidad(e);
        });

        $('#btnDetallesEntidad').on('click', (e) => {
            this.obtenerDetallesEntidad(e);
        });
    }

    nuevaEvent = () => {
        //Enfocar el campo RUC al mostrarse el modal
        $('#modalNuevaEntidad').on('shown.bs.modal', () => {
            $('#modalNuevaEntidad').find('input[name=ruc]').trigger('focus');
        });

        //Botón registrar
        $('#btnNuevaEntidadRegistrar').on('click', (e) => {
            const $boton = $(e.currentTarget);
            const $form = $('#formNuevaEntidad');
            if (Util.validarCampos($form)) {
                const dataEnviar = $form.serialize();
                $boton.html(Util.generarPuntosSvg() + ' Registrando');
                $boton.prop('disabled', true);
                $form.find('input').prop('disabled', true);
                this.model.registrar(dataEnviar).then((data) => {
                    if (data.tipo == 'success') {
                        Util.notify(data.tipo, data.mensaje);
                        $('#modalNuevaEntidad').modal('hide');
                        //Agregarlo al select
                        const nuevaEntidad = new Option(data.nombre, data.id, true, true);
                        $('#selectEntidad').append(nuevaEntidad).trigger('change');
                    }
                    else {
                        Util.mensaje('#divNuevaEntidadMensaje', data.tipo, data.mensaje);
                    }
                }).always(() => {
                    $boton.prop('disabled', false);
                    $boton.html('Registrar');
                    $form.find('input').prop('disabled', false);
                }).fail(() => {
                    Util.mensaje('#divNuevaEntidadMensaje', 'danger', 'Hubo un problema al registrar la entidad. Por favor actualice la página e intente de nuevo');
                });
            }
        });

        //Haciendo clic en Nuevo cliente o Nueva entidad
        $("#aNuevaEntidad").on('click', (e) => {
            e.preventDefault();
            const $modal = $('#modalNuevaEntidad');
            $modal.find('input[type=text],input[type=tel]').val('');
            $modal.find('div.mensaje').html('');
            $('#divNuevaEntidadMensaje').html('');
            $modal.modal('show');
        });

        //Verificar si existe DNI/RUC
        $('#modalNuevaEntidad input[name=ruc]').on('keyup', (e) => {
            const $mensaje = $(e.currentTarget).closest('div').find('div.mensaje');
            $mensaje.html('');
            if ($(e.currentTarget).val().length > 7) {

                this.model.buscarRuc($(e.currentTarget).val()).then((data) => {
                    if (data.tipo == 'danger') {
                        $mensaje.html(`<div class="text-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> ${data.mensaje}</div>`);
                    }
                });
            }
        });

        //Verificar si existe nombre de entidad
        $('#modalNuevaEntidad input[name=nombre]').on('keyup', (e) => {
            const $mensaje = $(e.currentTarget).closest('div').find('div.mensaje');
            $mensaje.html('');
            if ($(e.currentTarget).val().length > 3) {
                this.model.buscarNombre($(e.currentTarget).val()).then((data) => {
                    if (data.tipo == 'danger') {
                        $mensaje.html(`<div class="text-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> ${data.mensaje}</div>`);
                    }
                });
            }
        });

        $('#modalNuevaEntidad input').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnNuevaEntidadRegistrar').trigger('click');
            }
        });
    }

    actualizarEvent = () => {
        $('#modalEntidad').on('keyup', 'input', (e) => {
            if (e.key == 'Enter') {
                $('#btnActualizarEntidad').trigger('click');
            }
        });

        $('#btnActualizarEntidad').on('click', (e) => {
            const $boton = $(e.currentTarget);
            const $modal = $('#modalEntidad');
            const $mensaje = $modal.find('div.mensaje-final');
            const $form = $('#formEntidad');
            if (!Util.validarCampos($form)) {
                return false;
            }
            $boton.html(Util.generarPuntosSvg() + ' Actualizando');
            $boton.prop('disabled', true);
            this.model.actualizar($form.serialize()).then((data) => {
                if (data.tipo == 'success') {
                    $('#tableEntidades').DataTable().ajax.reload();
                    $modal.modal('hide');
                    Util.notify(data.tipo, data.mensaje);
                }
                else {
                    Util.mensaje($mensaje, data.tipo, data.mensaje);
                }

            }).fail(() => {
                Util.mensaje($mensaje, 'danger', 'Hubo un problema al actualizar la entidad. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $boton.html('Actualizar');
                $boton.prop('disabled', false);
            })
        });
    }

    buscarEvent = () => {
        const token = this.model.token;
        $("#selectEntidad").select2({
            language: "es",
            placeholder: "Buscar cliente",
            ajax: {
                url: route('mgcp.acuerdo-marco.entidades.buscar-entidad'), 
                dataType: 'json', 
                delay: 250, 
                method: 'POST', 
                data: function (params) {
                    return {
                        q: params.term, 
                        page: params.page, 
                        _token: token
                    };
                }, 
                processResults: function (data, page) {
                    var lista = new Array();
                    for (let i = 0; i < data[0].TotalRows; i++) {
                        lista[i] = {
                            id: data[0].Rows[i][0], 
                            text: data[0].Rows[i][2]
                        };
                    }
                    return {
                        results: lista
                    };
                }, cache: true
            }, escapeMarkup: function (markup) {
                return markup;
            }, minimumInputLength: 2
        });
    }
}
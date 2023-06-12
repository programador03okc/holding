class FondoProveedorView {
    constructor(model, urlSpinner) {
        this.model = model;
        this.urlSpinner = urlSpinner;
    }

    listar = () => {
        const model = this.model;
        const $tableFondos = $('#tableFondos').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableFondos_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableFondos.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableFondos_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#tableFondos_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.data-lista'),
                type: "POST",
                data: {
                    _token: model.token
                }
            },
            columns: [
                { data: 'descripcion' },
                { data: 'valor_unitario', className: 'text-right', searchable: false },
                { data: 'cantidad_inicial', className: 'text-center', searchable: false },
                { data: 'cantidad_utilizada', className: 'text-center', searchable: false },
                { data: 'cantidad_disponible', className: 'text-center', searchable: false },
                { data: 'subtotal_disponible', className: 'text-right', searchable: false },
                { data: 'activo', className: 'text-center', searchable: false }
            ],
            columnDefs: [
                { className: "text-center", targets: [7] },
                {
                    render: function (data, type, row) {
                        return row.valor_unitario_format;
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        return `<a href="#" data-id="${row.id}" data-fondo="${row.descripcion}" data-cantidad="${Util.formatoNumero(row.cantidad_inicial, 0)}" class="ingresada">${Util.formatoNumero(row.cantidad_inicial, 0)}</a>`;
                    }, targets: 2
                },
                {
                    render: function (data, type, row) {
                        return `<a href="#" data-id="${row.id}" class="utilizada">${Util.formatoNumero(row.cantidad_utilizada, 0)}</a>`;
                    }, targets: 3
                },
                {
                    render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad_disponible, 0);
                    }, targets: 4
                },
                {
                    render: function (data, type, row) {
                        return row.subtotal_disponible_format;
                    }, targets: 5
                },
                {
                    render: function (data, type, row) {
                        return row.activo ? "Sí" : "No";
                    }, targets: 6
                },
                {
                    render: function (data, type, row) {
                        let botones = '<div class="btn-group" role="group">';
                        if (row.activo) {
                            botones += `<button title="Desactivar fondo" data-id="${row.id}" class="btn btn-danger btn-xs desactivar"><i class="fa fa-lock" aria-hidden="true"></i></button>`;
                        }
                        else {
                            botones += `<button title="Reactivar fondo" data-id="${row.id}" class="btn btn-success btn-xs reactivar"><i class="fa fa-unlock" aria-hidden="true"></i></button>`;
                        }
                        botones += '</div>';
                        return botones;
                    }, targets: 7
                },
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo fondo',
                    action: function () {
                        $('#modalNuevoFondo').modal('show');
                    }, className: 'btn-sm'
                }]
        });

        $tableFondos.on('search.dt', () => {
            $('#tableFondos_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
        });

        $tableFondos.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc",
                    zIndex: 20
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });
    }

    cambiarEstadoEvent = () => {
        $('#tableFondos').on('click', 'button.reactivar, button.desactivar', (e) => {
            const $modal = $('#modalCambiarEstadoFondo');
            if ($(e.currentTarget).hasClass('reactivar')) {
                $modal.find('.accion').html('Reactivar')
            }
            else {
                $modal.find('.accion').html('Desactivar')
            }
            $modal.find('span.fondo').html($(e.currentTarget).closest('tr').find('td:eq(0)').html());
            $('#btnCambiarEstadoFondo').data('id', $(e.currentTarget).data('id'));
            $modal.modal('show');
        });

        $('#btnCambiarEstadoFondo').on('click', (e) => {
            const $boton = $(e.currentTarget);
            const accion = $boton.html();
            $boton.prop('disabled', true);
            if (accion == 'Desactivar') {
                $boton.html(Util.generarPuntosSvg() + 'Desactivando');
            }
            else {
                $boton.html(Util.generarPuntosSvg() + 'Reactivando');
            }
            this.model.cambiarEstado($boton.data('id')).then((resultado) => {
                if (resultado.tipo == 'success') {
                    $('#modalCambiarEstadoFondo').modal('hide');
                    $('#tableFondos').DataTable().ajax.reload();
                }
                Util.notify(resultado.tipo, resultado.mensaje);
            }).always(() => {
                $boton.prop('disabled', false);
            });
        });
    }

    cantidadIngresadaEvent = () => {
        $('#tableFondos').on('click', 'a.ingresada', (e) => {
            e.preventDefault();
            const $modalLista = $('#modalListaCantidadesIngresadas');
            const $modalNuevoIngreso = $('#modalNuevoIngreso');
            $modalLista.find('span.fondo').html($(e.currentTarget).data('fondo'));
            $modalLista.modal('show');

            $modalNuevoIngreso.find('div.fondo').html($(e.currentTarget).data('fondo'));
            $modalNuevoIngreso.find('div.cantidad').html($(e.currentTarget).data('cantidad'));
            $('#btnNuevoIngresoRegistrar').data('id', $(e.currentTarget).data('id'));
            this.listarCantidadesIngresadas(e);
        })
    }

    nuevoIngresoEvent = () => {

        $('#modalNuevoIngreso').on('shown.bs.modal', function (e) {
            $('#txtNuevoIngreso').trigger('focus');
        });

        $('#txtNuevoIngreso').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnNuevoIngresoRegistrar').trigger('click');
            }
        });

        $('#btnNuevoIngresoRegistrar').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Registrando');
            this.model.registrarIngreso($boton.data('id'), $('#txtNuevoIngreso').val()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#modalNuevoIngreso').modal('hide');
                    $('#tableCantidadesIngresadas').DataTable().ajax.reload();
                    $('#tableFondos').DataTable().ajax.reload();
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al registrar el ingreso. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Registrar');
            });
        });
    }

    cantidadUtilizadaEvent = () => {
        $('#tableFondos').on('click', 'a.utilizada', (e) => {
            e.preventDefault();
            $('#modalListaCantidadesUtilizadas').find('span.fondo').html($(e.currentTarget).closest('tr').find('td:eq(0)').html());
            $('#modalListaCantidadesUtilizadas').modal('show');
            this.listarCantidadesUtilizadas(e);
        })
    }

    listarCantidadesUtilizadas = (e) => {
        const model = this.model;
        const objeto = this;
        const $elemento = $(e.currentTarget);

        if ($.fn.DataTable.isDataTable('#tableCantidadesUtilizadas')) {
            $('#tableCantidadesUtilizadas').DataTable().destroy();
            $('#tableCantidadesUtilizadas').find('tbody').html('');
        }

        let $tableUtilizadas = $('#tableCantidadesUtilizadas').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableCantidadesUtilizadas_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btntableCantidadesUtilizadas" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btntableCantidadesUtilizadas').trigger('click');
                    }
                });
                $('#btntableCantidadesUtilizadas').on('click', (e) => {
                    $tableUtilizadas.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableCantidadesUtilizadas_filter input').prop('disabled', false);
                $('#btntableCantidadesUtilizadas').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#tableCantidadesUtilizadas_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[0, "desc"]],
            ajax: {
                url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.listar-utilizados-fondo'),
                type: "POST",
                data: { id_fondo_proveedor: $elemento.data('id'), _token: model.token }
            },
            columns: [
                { data: 'tipo', className: 'text-center' },
                { data: 'codigo_oportunidad', className: 'text-center' },
                { data: 'estado', className: 'text-center' },
                { data: 'descripcion' },
                { data: 'razon_social' },
                { data: 'comentario' },
                { data: 'cantidad', className: 'text-center', searchable: false },
                { data: 'precio', className: 'text-right', searchable: false },
                { data: 'comprado', className: 'text-center', searchable: false },
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0);
                    }, targets: 6
                },
                {
                    render: function (data, type, row) {
                        return row.comprado ? 'Sí' : 'No';
                    }, targets: 8
                },
            ],
            buttons: []
        });

        $tableUtilizadas.on('search.dt', function () {
            $('#tableCantidadesUtilizadas_filter input').attr('disabled', true);
            $('#btntableCantidadesUtilizadas').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }

    listarCantidadesIngresadas = (e) => {
        const model = this.model;
        const objeto = this;
        const $elemento = $(e.currentTarget);

        if ($.fn.DataTable.isDataTable('#tableCantidadesIngresadas')) {
            $('#tableCantidadesIngresadas').DataTable().destroy();
            $('#tableCantidadesIngresadas').find('tbody').html('');
        }

        let $tableIngresos = $('#tableCantidadesIngresadas').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableCantidadesIngresadas_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btntableCantidadesIngresadas" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btntableCantidadesIngresadas').trigger('click');
                    }
                });
                $('#btntableCantidadesIngresadas').on('click', (e) => {
                    $tableIngresos.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableCantidadesIngresadas_filter input').prop('disabled', false);
                $('#btntableCantidadesIngresadas').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#tableCantidadesIngresadas_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[2, "desc"]],
            ajax: {
                url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.listar-ingresos-fondo'),
                type: "POST",
                data: { id_fondo_proveedor: $elemento.data('id'), _token: model.token }
            },
            columns: [
                { data: 'cantidad', className: 'text-center', searchable: false },
                { data: 'name', name: 'users.name', className: 'text-center', searchable: false },
                { data: 'fecha', className: 'text-center', searchable: false },
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0);
                    }, targets: 0
                },
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo ingreso',
                    action: function () {
                        //objeto.nuevoIngreso($elemento.data('id'));
                        $('#modalNuevoIngreso').modal('show');
                    }, className: 'btn-sm'
                }
            ]
        });

        $tableIngresos.on('search.dt', function () {
            $('#tableCantidadesIngresadas_filter input').attr('disabled', true);
            $('#btntableCantidadesIngresadas').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }



    obtenerFondosDisponiblesEvent = () => {
        $('#modalProveedores').on('show.bs.modal', () => {
            this.obtenerFondosDisponibles();
        });
    }

    obtenerFondosParaProformaEvent = () => {
        const model = this.model;
        const $tableFondos = $('#tableFondosProforma').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableFondosProforma_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscarFondo" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscarFondo').trigger('click');
                    }
                });
                $('#btnBuscarFondo').on('click', (e) => {
                    $tableFondos.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableFondosProforma_filter input').prop('disabled', false);
                $('#btnBuscarFondo').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#tableFondosProforma_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.data-lista-para-proformas'),
                type: "POST",
                data: {
                    _token: model.token
                }
            },
            columns: [
                { data: 'descripcion' },
                { data: 'valor_unitario', className: 'text-right', searchable: false },
                { data: 'cantidad_disponible', className: 'text-center', searchable: false },
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return row.valor_unitario_format;
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad_disponible, 0);
                    }, targets: 2
                },
            ],
            buttons: []
        });

        $tableFondos.on('search.dt', () => {
            $('#tableFondosProforma_filter input').prop('disabled', true);
            $('#btnBuscarFondo').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
        });

        $('#modalFondosDisponibles').on('shown.bs.modal', function (e) {
            $('#tableFondosProforma').DataTable().ajax.reload();
        });
    }

    obtenerFondosDisponibles = () => {
        $('#tdFondoProveedor').html(`<div class="text-center"><img src="${this.urlSpinner}"></div>`);
        this.model.obtenerFondosDisponibles().then((data) => {
            let fondo = `<select class="form-control" name="fondo">
                        <option value="0">Ninguno</option>`;
            for (let indice in data) {
                fondo += `<option value="${data[indice].id}">${data[indice].descripcion} (valor: ${data[indice].valor_unitario_format}, disponible: ${Util.formatoNumero(data[indice].cantidad_disponible)})</option>`;
            }
            fondo += '</select>';
            $('#tdFondoProveedor').html(fondo);
        })
    }

    nuevoEvent = () => {
        $('#modalNuevoFondo').on('shown.bs.modal', function (e) {
            $('#txtDescripcion').trigger('focus');
        });

        $('#modalNuevoFondo').on('show.bs.modal', function (e) {
            $('#txtDescripcion').val('');
            $('#txtValorUnitario').val('');
            $('#txtCantidadInicial').val('');
        });

        $('#txtDescripcion, #txtValorUnitario, #txtCantidadInicial').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnRegistrarFondo').trigger('click');
            }
        });

        $('#modalNuevoFondo').on('click', 'a.moneda', (e) => {
            e.preventDefault();
            $('#spanMonedaSeleccionada').html($(e.currentTarget).html());
            $('#txtMoneda').val($(e.currentTarget).html() == '$' ? 'd' : 's');
        });

        $('#btnRegistrarFondo').on('click', (e) => {
            if (!Util.validarCampos('#formNuevoFondo')) {
                return false;
            }
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $(e.currentTarget).html(Util.generarPuntosSvg() + 'Registrando');
            this.model.registrarFondo($('#formNuevoFondo').serialize()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#modalNuevoFondo').modal('hide');
                    $('#tableFondos').DataTable().ajax.reload();
                }
                $boton.html('Registrar');
            }).always(() => {
                $boton.prop('disabled', false);
            });
        });
    }
}
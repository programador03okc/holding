class OportunidadView {
    constructor(model) {
        this.model = model;
    }

    listarTodas = (idUsuario, permisos) => {
        const rutaDetalles = route('mgcp.oportunidades.detalles');
        const $tableOportunidades = $('#tableOportunidades').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableOportunidades_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableOportunidades.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableOportunidades_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
                $('#tableOportunidades_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [
                [6, "desc"]
            ],
            ajax: {
                url: route('mgcp.oportunidades.data-lista'),
                type: "POST",
                data: function ( params ) {
                    return Object.assign(params, Util.objectifyForm($('#formFiltros').serializeArray()))
                }
            },
            columns: [
                { data: 'nombre_entidad', name: 'entidades.nombre' },
                { data: 'oportunidad' },
                { data: 'probabilidad', searchable: false, className: 'text-center' },
                { data: 'oportunidad', orderable: false },
                { data: 'importe', className: 'text-right', searchable: false },
                { data: 'created_at', searchable: false, className: 'text-center' },
                { data: 'fecha_limite', searchable: false, className: 'text-center' },
                { data: 'margen', searchable: false, className: 'text-center' },
                { data: 'name', name: 'users.name' },
                { data: 'estado', name: 'estados.estado', className: 'text-center' },
                { data: 'grupo', name: 'grupos.grupo', className: 'text-center' },
                { data: 'tipo', name: 'tipos_negocio.tipo', className: 'text-center' },
                { data: 'codigo_oportunidad', className: 'text-center', orderable: false }
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return `<span class="cliente">${row.nombre_entidad}</span>`;
                    }, targets: 0
                },
                {
                    render: function (data, type, row) {
                        return `<a class="azul" href="${rutaDetalles}/${row.id}"><strong class="underline">${row.codigo_oportunidad}</strong><br><span class="descripcion">${row.oportunidad}<span></a>`;
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        return `<span class="probabilidad">${row.probabilidad.charAt(0).toUpperCase() + row.probabilidad.slice(1)}</span>`;
                    }, targets: 2
                },
                {
                    render: function (data, type, row) {
                        if (row.ultimo_status.length > 100) {
                            return `${row.ultimo_status.substring(0, 105)}...<a class="ver-status" href="#" data-status="${row.ultimo_status.split('"').join('')}">Ver más</a>`;
                        } else {
                            return row.ultimo_status;
                        }
                    }, targets: 3
                },
                {
                    render: function (data, type, row) {
                        return `<span class="monto">${row.monto}</span>`;
                    }, targets: 4
                },
                {
                    render: function (data, type, row) {
                        return row.margen + '%';
                    }, targets: 7
                },
                {
                    render: function (data, type, row) {
                        let botones = '';
                        if (idUsuario == row.id_responsable || permisos.puedeEditar == 1) {
                            botones += `<button style="margin-right: 2px" data-codigo="${row.codigo_oportunidad}" data-id="${row.id}" class="btn btn-primary editar-oportunidad btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>`;
                        }
                        if (permisos.puedeEliminar == 1) {
                            botones += `<button data-id="${row.id}" class="btn btn-danger eliminar-oportunidad btn-xs"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>`;
                        }
                        return botones;
                    }
                    , targets: 12
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                    action: function () {
                        $('#modalFiltros').modal('show');
                    }, className: 'btn-sm'
                }]
        });

        $tableOportunidades.on('search.dt', () => {
            $('#tableOportunidades_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
        });

        $tableOportunidades.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {
                    imageAutoResize: true,
                    imageColor: "#3c8dbc"
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });
    }

    listarParaOc = () => {
        $('#modalCrearCuadroCosto').on('show.bs.modal', (e) => {
            const model = this.model;

            if ($.fn.DataTable.isDataTable('#tableOportunidades')) {
                $('#tableOportunidades').DataTable().destroy();
                $('#tableOportunidades').find('tbody').html('');
            }

            const $tableOportunidades = $('#tableOportunidades').DataTable({
                pageLength: 10,
                dom: 'Bfrtip',
                processing: true,
                serverSide: true,
                initComplete: function (settings, json) {
                    const $filter = $('#tableOportunidades_filter');
                    const $input = $filter.find('input');
                    $filter.append('<button id="btnBuscarOport" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                    $input.unbind();
                    $input.bind('keyup', function (e) {
                        if (e.keyCode == 13) {
                            $('#btnBuscarOport').trigger('click');
                        }
                    });
                    $('#btnBuscarOport').click(function () {
                        $tableOportunidades.search($input.val()).draw();
                    });
                },
                drawCallback: function (settings) {
                    $('#tableOportunidades_filter input').attr('disabled', false);
                    $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                    $('#tableOportunidades_filter input').focus();
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                order: [[3, "desc"]],
                ajax: {
                    url: route('mgcp.oportunidades.data-lista-para-oc'),
                    type: "POST",
                    data: { idOrden: $('#txtIdOc').val(), tipo: $('#txtTipoOc').val(), _token: model.token },
                },
                columns: [
                    { data: 'nombre_entidad', name: 'entidades.nombre' },
                    { data: 'oportunidad', name: 'oportunidad' },
                    { data: 'codigo_oportunidad', name: 'codigo_oportunidad' },
                    { data: 'created_at', name: 'oportunidades.created_at', className: "text-center", searchable: false },
                    { data: 'fecha_limite', name: 'fecha_limite', searchable: false, className: "text-center" },
                    { data: 'name', name: 'users.name' },
                    { data: 'estado', name: 'estados.estado', className: "text-center" },
                    { data: 'grupo', name: 'grupos.grupo', className: "text-center" },
                    { data: 'tipo', name: 'tipos_negocio.tipo', className: "text-center" }
                ],
                columnDefs: [
                    { orderable: false, targets: [9] },
                    { className: "text-center", targets: [9] },
                    //{className: "text-right", targets: [2]},
                    {
                        targets: 6
                    },
                    {
                        render: function (data, type, row) {
                            return `<strong class="underline">${row.codigo_oportunidad}</strong><br>${row.oportunidad}`;
                        }, targets: 1
                    },
                    {
                        render: function (data, type, row) {
                            return row.monto;
                        }, targets: 2
                    },
                    {
                        render: function (data, type, row) {
                            return `<button type="button" class="btn btn-primary btn-xs vincular" data-id="${row.id}">Vincular</button>`;
                        }, targets: 9
                    }
                ],
                buttons: [
                ]
            });
            $tableOportunidades.on('search.dt', function () {
                $('#tableOportunidades_filter input').attr('disabled', true);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
            });
        });
    }

    verStatusEvent = () => {
        $('#tableOportunidades').on("click", "a.ver-status", (e) => {
            e.preventDefault();
            $('#modalStatus').modal('show');
            $('#divStatusOportunidad').html($(e.currentTarget).data('status'));
        });
    }

    eliminarEvent = () => {
        //*****ELIMINAR*****
        $('#tableOportunidades').on("click", "button.eliminar-oportunidad", (e) => {
            const $modal = $('#modalEliminarOportunidad');
            const $fila = $(e.currentTarget).closest('tr');
            //$modal.find('div.oportunidad').html('<strong>Oportunidad:</strong> ' + $(this).data('oportunidad'));
            $modal.find('div.detalles').html(`
            <ul>
                <li>Cliente: ${$fila.find('span.cliente').html()}</li>
                <li>Oportunidad: ${$fila.find('span.descripcion').html()}</li>
                <li>Monto: ${$fila.find('span.monto').html()}</li>
                <li>Probabilidad: ${$fila.find('span.probabilidad').html()}</li>
            </ul>
            `)
            $modal.modal('show');
            $('#btnEliminarOportunidadAceptar').data('id', $(e.currentTarget).data('id'));
        });

        $('#btnEliminarOportunidadAceptar').click((e) => {
            const $modal = $('#modalEliminarOportunidad');
            const $mensaje = $modal.find('div.mensaje-final');
            const $boton = $(e.currentTarget);
            $modal.find('button').prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Eliminando')
            this.model.eliminar($boton.data('id')).then((data) => {
                if (data.tipo == 'success') {
                    Util.notify(data.tipo, data.mensaje);
                    $('#tableOportunidades').DataTable().ajax.reload();
                    $modal.modal('hide');
                }
                else {
                    Util.mensaje($mensaje, data.tipo, data.mensaje);
                }
            }).fail(() => {
                Util.mensaje($mensaje, 'danger', 'Hubo un problema al eliminar la oportunidad. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $modal.find('button').prop('disabled', false);
                $boton.html('Eliminar');
            });
        });
        //*****FIN ELIMINAR*****
    }

    editarEvent = (actualizarPagina = false) => {
        //Obtener detalles de oportunidad
        $('body').on("click", "button.editar-oportunidad", (e) => {
            $('#txtIdOportunidad').val($(e.currentTarget).data('id'));
            const $modal = $('#modalEditarOportunidad');
            const $boton = $('#btnEditarOportunidadAceptar');
            //const $form = $('#formEditarOportunidad');
            const $mensajeInicial = $modal.find('div.mensaje-inicial');
            const $mensajeFinal = $modal.find('div.mensaje-final');
            $modal.find('span.codigo-oportunidad').html($(e.currentTarget).data('codigo'));
            Util.bloquearConSpinner($mensajeInicial);
            //$mensajeInicial.show();
            $mensajeFinal.html('');
            //$form.hide();
            $boton.prop('disabled', true);
            $modal.modal('show');

            this.model.obtenerDetalles($(e.currentTarget).data('id')).then((data) => {
                $modal.find('select[name=cliente]').val(data.id_entidad);
                $modal.find('textarea[name=oportunidad]').val(data.oportunidad);
                $modal.find('select[name=responsable]').val(data.id_responsable);
                $modal.find('select[name=probabilidad]').val(data.probabilidad);
                $modal.find('select[name=tipo_moneda]').val(data.moneda);
                $modal.find('input[name=importe]').val(data.importe);
                $modal.find('input[name=margen]').val(data.margen);
                $modal.find('input[name=fecha_limite]').val(data.fecha_limite);
                $modal.find('input[name=nombre_contacto]').val(data.nombre_contacto);
                $modal.find('input[name=cargo_contacto]').val(data.cargo_contacto);
                $modal.find('input[name=telefono_contacto]').val(data.telefono_contacto);
                $modal.find('input[name=correo_contacto]').val(data.correo_contacto);
                $modal.find('select[name=grupo]').val(data.id_grupo);
                $modal.find('select[name=tipo_negocio]').val(data.id_tipo_negocio);
                $modal.find('input[name=reportado_por]').val(data.reportado_por);
                Util.liberarBloqueoSpinner($mensajeInicial);
                $boton.prop('disabled', false);
            }).fail(() => {
                alert('Hubo un problema al obtener los detalles de la oportunidad. Por favor actualice la página e intente de nuevo');
                $modal.modal('hide');
            });
        });

        $('#btnEditarOportunidadAceptar').click((e) => {
            const $modal = $('#modalEditarOportunidad');
            const $boton = $(e.currentTarget);
            const $form = $('#formEditarOportunidad');
            const $mensaje = $modal.find('div.mensaje-final');
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Actualizando');

            if (Util.validarCampos($form)) {
                const dataEnviar = $form.serialize();
                $form.find('input, select, textarea').prop('disabled', true);
                this.model.actualizar(dataEnviar).then((data) => {
                    if (data.tipo == 'success') {
                        if (actualizarPagina) {
                            alert(data.mensaje + ', la página se recargará...');
                            location.reload();
                        }
                        else {
                            Util.notify(data.tipo, data.mensaje);
                            $('#tableOportunidades').DataTable().ajax.reload();
                            $modal.modal('hide');
                        }
                    }
                    else {
                        Util.mensaje($mensaje, data.tipo, data.mensaje);
                    }
                }).fail(() => {
                    Util.mensaje($mensaje, 'danger', 'Hubo un problema al actualizar los datos. Por favor actualice la página e intente de nuevo');
                }).always(() => {
                    $form.find('input, select, textarea').prop('disabled', false);
                    $boton.prop('disabled', false);
                    $boton.html('Actualizar');
                });
            }
        });
    }

    registrarOtrosEvent = () => {

        $('form').on('submit', (e) => {
            $('button[type=submit]').prop('disabled', true).html(Util.generarPuntosSvg() + 'Registrando');
        });

        $('#selectEstado').on('change', (e) => {
            if ($(e.currentTarget).val() == '6') {
                $(e.currentTarget).closest('div').append('<span class="text-danger">Coordine con su gerente antes de dar la oportunidad por desestimada</span>');
            } else {
                $(e.currentTarget).closest('div').find('span').remove();
            }
        });
    }

    verArchivosEvent = () => {
        $('body').on('click', 'button.ver-archivos-oportunidad', (e) => {
            const $boton = $(e.currentTarget);
            const $modal = $('#modalArchivosOportunidad');
            const $contenido = $modal.find('div.contenido');
            const $mensaje = $modal.find('div.mensaje');
            Util.bloquearConSpinner($mensaje);
            //$mensaje.html();
            $modal.modal('show');
            this.model.obtenerArchivos($boton.data('codigo'), $boton.data('tipo')).then((data) => {
                $contenido.html(data.mensaje);
                Util.liberarBloqueoSpinner($mensaje);
            }).fail(() => {
                alert("Hubo un problema al obtener los archivos. Por favor intente de nuevo");
                $modal.modal('hide');
            });
        });
    }

    nuevaEvent = () => {
        $('#formCrearOportunidad').on('submit', (e) => {
            if ($('#selectEntidad').val() == null) {
                e.preventDefault();
                alert('Seleccione una entidad antes de continuar');
                return;
            }

            let $boton = $('#btnRegistrarOportunidad');
            $boton.html(Util.generarPuntosSvg() + 'Registrando');
            $boton.prop('disabled', true);
        });
    }

    crearOportunidadDesdeOcEvent = (rutaCuadroCosto) => {
        $('#btnCrearOportunidadDesdeOc').on('click', (e) => {
            if ($('#txtOportunidadDescripcion').val() == "") {
                alert("Ingrese una descripción para continuar");
                return false;
            }

            const $boton = $(e.currentTarget);
            $boton.attr('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Creando oportunidad');
            this.model.crearOportunidadDesdeOc($('#txtIdOc').val(),$('#txtTipoOc').val(), $('#txtOportunidadDescripcion').val(), $('#selectOportunidadResponsable').val()).then((data) => {
                alert(data.mensaje);
                if (data.tipo == 'success') {
                    //window.location = rutaCuadro + "/orden/" + $('#txtIdOc').val() + "/oportunidad/" + data.id;
                    window.location = rutaCuadroCosto + "/" + data.id;
                }
            }).fail(() => {
                alert("Hubo un problema al crear la oportunidad. Por favor actualice la página e inténtelo de nuevo");
            }).always(() => {
                $boton.attr('disabled', false);
                $boton.html('Crear oportunidad');
            });
        });
    }
}
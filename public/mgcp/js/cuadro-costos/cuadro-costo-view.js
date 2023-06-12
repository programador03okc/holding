class CuadroCostoView extends CuadroBaseView {
    constructor(id, model) {
        super(id, model);
    }

    listar = () => {
        const model = this.model;
        const urlSistema = route('mgcp.base');
        const $tableDatos = $('#tableDatos').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableDatos_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
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
                $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
                $('#tableDatos_filter input').trigger('focus');
            },
            order: [
                [1, "desc"]
            ],
            ajax: {
                url: route("mgcp.cuadro-costos.data-lista"),
                type: "POST",
                data: function (params) {
                    return Object.assign(params, Util.objectifyForm($('#formFiltros').serializeArray()))
                },
                dataType: 'json'
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            columns: [
                { data: 'codigo_oportunidad', className: 'text-center' },
                { data: 'fecha_creacion', className: 'text-center', searchable: false },
                { data: 'descripcion_oportunidad' },
                { data: 'fecha_limite', searchable: false, className: 'text-center' },
                { data: 'nombre_entidad' },
                { data: 'name' },
                { data: 'tipo_cuadro', className: 'text-center', searchable: false },
                { data: 'nro_orden', className: 'text-center' },
                { data: 'tiene_transformacion', className: 'text-center', orderable: false, searchable: false },
                { data: 'monto_gg_soles', className: 'text-right', orderable: true, searchable: false },
                { data: 'monto_ganancia', className: 'text-right', orderable: false, searchable: false },
                { data: 'margen_ganancia', className: 'text-center', orderable: false, searchable: false },
                { data: 'estado_aprobacion', searchable: false },
                { data: 'responsable_aprobacion', className: 'text-left' },
                { data: 'estado', searchable: false },
            ],
            columnDefs: [{ className: "text-center", targets: [14] },
            {
                render: function (data, type, row) {
                    return row.fecha_creacion;
                }, targets: 1
            },
            {
                render: function (data, type, row) {
                    return row.fecha_limite;
                }, targets: 3
            },
            {
                render: function (data, type, row) {
                    if (row.tipo_cuadro == 'directa') {
                        return 'Venta directa';
                    } else {
                        return 'Acuerdo marco'
                    }
                }, targets: 6
            },
            {
                render: function (data, type, row) {
                    return row.tiene_transformacion ? 'Sí' : 'No';
                }, targets: 8
            },
            {
                render: function (data, type, row) {
                    return row.moneda == 's' ? 'S/ ' + $.number(row.monto_gg_soles,2, '.', ',') : '$ ' + $.number(row.monto_gg_dolares,2, '.', ',');
                }, targets: 9
            },
            {
                render: function (data, type, row) {
                    return row.responsable_aprobacion == null ? '-' : row.responsable_aprobacion;
                }, targets: 13
            },
            {
                render: function (data, type, row) {

                    return '<a class="btn btn-primary btn-sm" href="' + urlSistema + '/cuadro-costos/detalles/' + row.id_oportunidad + '">Ver</a>';
                }, targets: 14
            },
            ],
            buttons: [{
                text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                action: function () {
                    $('#modalFiltros').modal('show');
                }, className: 'btn-sm'
            },
            {
                text: '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar lista',
                action: function () {
                    $('#modalExportarLista').modal('show');
                }, className: 'btn-sm'
            }
            ],
            //lengthChange: false,
            //scrollCollapse: true,
            //paging: true
        });

        $tableDatos.on('search.dt', function () {
            $('#tableDatos_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
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

    actualizarCampoEvent = () => {

        $('#txtTipoCambio').on('change', (e) => {
            $('#spanTipoCambio').html(Util.formatoNumero($(e.currentTarget).val(), 3));
            this.actualizarCampo($(e.currentTarget));
        });

        $('#selectMonedaCuadro').on('change', (e) => {
            $('#spanMonedaCuadro').html($(e.currentTarget).val() == 's' ? 'S/' : '$');
            $('span.moneda').html($(e.currentTarget).val() == 's' ? 'soles' : 'dólares')
            this.actualizarCampo($(e.currentTarget));
        });

        $('#txtPorcentajeResponsable').on('change', (e) => {
            this.actualizarCampo($(e.currentTarget));
        });

        $('#menuCondicionCredito').on('change', 'input[type=radio]', (e) => {
            let tipoCredito = $(e.currentTarget).val();
            let dato = tipoCredito == 1 ? 0 : $(e.currentTarget).closest('div.form-group').find('select').val();
            this.actualizarCondicionCredito(this.id, tipoCredito, dato);
        });
        $('#menuCondicionCredito').on('change', 'select', (e) => {
            let dato = $(e.currentTarget).val();
            let $radio = $(e.currentTarget).closest('div.form-group').find('input[type=radio]');
            if ($radio.is(':checked')) {
                this.actualizarCondicionCredito(this.id, $radio.val(), dato);
            }
        });
    }

    actualizarCondicionCredito(id, tipoCredito, dato) {
        this.model.actualizarCondicionCredito(id, tipoCredito, dato).then((data) => {
            Util.notify(data.tipo, data.mensaje);
            if (data.tipo == 'success') {
                this.obtenerDetallesFilas();
            }
        }).fail(() => {
            Util.notify("error", "Hubo un problema al actualizar la condición de crédito. Por favor inténtelo de nuevo");
        });
    }

    finalizarCuadroEvent = () => {
        $('#btnFinalizarCuadro').on('click', (e) => {
            $(e.currentTarget).html(Util.generarPuntosSvg() + 'Finalizando');
            $('#modalFinalizarCuadro').find('button').prop('disabled', true);
            this.model.finalizarCuadro(this.id).then((data) => {
                Swal.fire({
                    icon: data.tipo,
                    title: data.titulo,
                    text: data.texto,
                }).then((result) => {
                    if (data.tipo == 'success') {
                        location.reload();
                    }
                })
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al procesar su solicitud',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
            }).always(() => {
                $('#modalFinalizarCuadro').find('button').prop('disabled', false);
                $(e.currentTarget).html('Finalizar');
            });
        });
    }

    seleccionarCentroCostoEvent() {
        $('#modal-centro-costos').on('click', 'button.seleccionar', (e) => {
            const $boton = $(e.currentTarget);
            const $fila = $boton.closest('tr');
            $boton.prop('disabled', true);
            this.model.seleccionarCentroCosto(this.id, $boton.data('id')).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#modal-centro-costos').modal('hide');
                    $('span.centro-costo').html(data.descripcion);
                }
            }).always(() => {
                $boton.prop('disabled', false);
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al procesar su solicitud',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
            });
        })
    }

    enviarOrdenDespachoEvent() {
        $('#btnEnviarOrdenDespacho').on('click',(e)=>{
            $(e.currentTarget).prop('disabled',true);
            $(e.currentTarget).html(Util.generarPuntosSvg() + 'Enviando');
            let formData=new FormData(document.getElementById("formOrdenDespacho"));
            this.model.enviarOrdenDespacho(formData).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                console.log(data.mensaje);
                if (data.tipo == 'success') {
                    $('#modalEnviarOrdenDespacho').modal('hide');
                }
            }).always(() => {
                $(e.currentTarget).html('Enviar');
                $(e.currentTarget).prop('disabled',false);
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al procesar su solicitud',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
            });
        })
    }
}
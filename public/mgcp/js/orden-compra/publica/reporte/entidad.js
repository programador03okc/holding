var ReporteEntidad = (function () {

    var token;

    function init(_token) {
        token = _token;
    }

    function contarFiltros() {
        $('#spanFiltrosEntidades').html($('#modalFiltrosEntidades').find('input[type=checkbox]:checked').length);
    }

    function actualizarFiltros(ruta) {
        contarFiltros();
        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: $('#modalFiltrosEntidades').find('form').serialize(),
            error: function () {
                alert("Hubo un problema al aplicar los filtros. Actualice la página y vuelva a intentarlo.");
            }
        });
    }

    function resumen(ruta) {
        var $tableResumen = $('#tableEntidades').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableEntidades_filter');
                var $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').click(function () {
                    $tableResumen.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableEntidades_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableEntidades_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[6, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: { _token: token },
                complete: function () {
                    ReporteEntidad.contarFiltros();
                }
            },
            columns: [
                { data: 'nombre_entidad' },
                { data: 'catalogo' },
                { data: 'categoria' },
                { data: 'marca' },
                { data: 'ctd_oc', searchable: false },
                { data: 'cantidad', searchable: false },
                { data: 'monto', searchable: false }
            ],
            columnDefs: [
                { className: "text-center", targets: [3, 4, 5] },
                { className: "text-right", targets: [6] },
                {
                    render: function (data, type, row) {
                        return '<a data-entidad="' + row.id_entidad + '" data-categoria="' + row.categoria + '" data-marca="' + row.marca + '" href="#" data-toggle="modal" data-target="#modalDetallesEntidad" class="reporteDetalles">' + Util.formatoNumero(row.ctd_oc, 0, '.', ',') + '</a>';
                    }, targets: 4
                },
                {
                    render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 5
                },
                {
                    render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.monto, 2, '.', ',');
                    }, targets: 6
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanFiltrosEntidades">0</span>',
                    action: function () {
                        $('#modalFiltrosEntidades').modal('show');
                    }
                }
            ]
        });

        $tableResumen.on('search.dt', function () {
            $('#tableEntidades_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }

    function detalles($elemento, ruta, tcUsd) {
        var $modal = $('#modalDetallesEntidad');
        var $fila=$elemento.closest('tr');
        $modal.find('span.entidad').html($fila.find('td:eq(0)').html());
        $modal.find('span.categoria').html($fila.find('td:eq(2)').html());
        $modal.find('span.marca').html($fila.find('td:eq(3)').html());

        var $tableDetalles=$('#tableDetallesEntidad').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableDetallesEntidad_filter');
                var $input = $filter.find('input');
                $filter.append('<button id="btnDetallesBuscar" class="btn btn-default btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $('#btnDetallesBuscar').trigger('click');
                    }
                });
                $('#btnDetallesBuscar').click(function () {
                    $tableDetalles.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableDetallesEntidad_filter input').attr('disabled', false);
                $('#btnDetallesBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableDetallesEntidad_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[4, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: { entidad: $elemento.data('entidad'), categoria: $elemento.data('categoria'), marca: $elemento.data('marca'), _token: token },
                complete: function () {
                    ReporteEntidad.contarFiltros();
                }
            },
            columns: [
                { data: 'fecha_formalizacion', searchable: false },
                { data: 'producto' },
                { data: 'orden_compra' },
                { data: 'nombre_entidad' },
                { data: 'cantidad', searchable: false },
                { data: 'precio_unitario', searchable: false },
                { data: 'precio_dolares', searchable: false },
                { data: 'costo_envio', searchable: false },
                { data: 'subtotal', searchable: false },
                { data: 'plazo_entrega', searchable: false }
            ],
            columnDefs: [
                { orderable: false, targets: [6, 8] },
                { className: "text-center", targets: [0, 2, 4, 5, 9] },
                { className: "text-right", targets: [6, 7, 8] },
                {
                    render: function (data, type, row) {
                        return '<a target="_blank" href="' + row.ficha_tecnica + '">' + row.producto + '</a>';
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 4 //cantidad
                },
                {
                    render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.precio_unitario, 2, '.', ',');
                    }, targets: 5 //precio pen
                },
                {
                    render: function (data, type, row) {
                        if (row.precio_dolares == null) {
                            return '$ ' + Util.formatoNumero(row.precio_unitario / tcUsd, 2, '.', ',');
                        } else {
                            return '$ ' + Util.formatoNumero(row.precio_dolares, 2, '.', ',');
                        }
                    }, targets: 6 //precio usd
                },
                {
                    render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.costo_envio, 2, '.', ',');
                    }, targets: 7 //costo envio
                },
                {
                    render: function (data, type, row) {
                        if (row.subtotal == null) {
                            return '$ ' + Util.formatoNumero(((row.precio_unitario / tcUsd) * row.cantidad) + (row.cantidad * (row.costo_envio / tcUsd)), 2, '.', ',');
                        } else {
                            return '$ ' + Util.formatoNumero(row.subtotal, 2, '.', ',');
                        }
                    }, targets: 8 //subtotal
                }

            ],
            buttons: [
            ]
        });

        $tableDetalles.on('search.dt', function () {
            $('#tableDetallesEntidad_filter input').attr('disabled', true);
            $('#btnDetallesBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }

    return {
        init: init,
        resumen: resumen,
        detalles: detalles,
        actualizarFiltros: actualizarFiltros,
        contarFiltros: contarFiltros,

    };
})();
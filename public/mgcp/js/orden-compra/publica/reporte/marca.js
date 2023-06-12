var ReporteMarca = (function () {

    var token;

    function init(_token)
    {
        token = _token;
    }

    function contarFiltros()
    {
        $('#spanFiltrosMarcas').html($('#modalFiltrosMarcas').find('input[type=checkbox]:checked').length);
    }

    function actualizarFiltros(ruta)
    {
        contarFiltros();
        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: $('#modalFiltrosMarcas').find('form').serialize(),
            error: function () {
                alert("Hubo un problema al aplicar los filtros. Actualice la página y vuelva a intentarlo.");
            }
        });
    }

    function resumen(ruta)
    {
        var $tableResumen=$('#tableMarcas').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableMarcas_filter');
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
                $('#tableMarcas_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableMarcas_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[5, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {_token: token},
                complete: function () {
                    contarFiltros();
                }
            },
            columns: [
                {data: 'marca'},
                {data: 'catalogo'},
                {data: 'categoria'},
                {data: 'ctd_oc', searchable: false},
                {data: 'cantidad', searchable: false},
                {data: 'monto', searchable: false}
            ],
            columnDefs: [
                {className: "text-center", targets: [0, 3, 4]},
                {className: "text-right", targets: [5]},
                {render: function (data, type, row) {
                        return '<a data-marca="' + row.marca + '" data-categoria="' + row.categoria + '" href="#" data-toggle="modal" data-target="#modalDetallesMarca" class="reporteDetalles">' + Util.formatoNumero(row.ctd_oc, 0, '.', ',') + '</a>';
                    }, targets: 3
                },
                {render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 4
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.monto, 2, '.', ',');
                    }, targets: 5
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanFiltrosMarcas">0</span>',
                    action: function () {
                        $('#modalFiltrosMarcas').modal('show');
                    }
                }
            ]
        });

        $tableResumen.on('search.dt', function () {
            $('#tableMarcas_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }

    function detalles($elemento, ruta, tcUsd)
    {
        var $modal = $('#modalDetallesMarca');
        $modal.find('input[name=categoria]').val($elemento.data('categoria'));
        $modal.find('input[name=marca]').val($elemento.data('marca'));
        $modal.find('span.categoria').html($elemento.data('categoria'));
        $modal.find('span.marca').html($elemento.data('marca'));

        var $tableDetalles=$('#tableDetallesMarca').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableDetallesMarca_filter');
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
                $('#tableDetallesMarca_filter input').attr('disabled', false);
                $('#btnDetallesBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableDetallesMarca_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[5, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {categoria: $elemento.data('categoria'), marca: $elemento.data('marca'), _token: token},
                complete: function () {
                    contarFiltros();
                }
            },
            columns: [
                {data: 'fecha_formalizacion', searchable: false},
                {data: 'nombre_entidad'},
                {data: 'producto'},
                {data: 'orden_compra'},
                {data: 'razon_social'},
                {data: 'cantidad', searchable: false},
                {data: 'precio_unitario', searchable: false},
                {data: 'precio_dolares', searchable: false},
                {data: 'costo_envio', searchable: false},
                {data: 'subtotal', searchable: false},
                {data: 'plazo_entrega', searchable: false}
            ],//12 ficha_tecnica, 2 producto
            columnDefs: [
                {orderable: false, targets: [7, 9]},
                {className: "text-center", targets: [0, 3, 5, 10]},
                {className: "text-right", targets: [6, 7, 8, 9]},
                {render: function (data, type, row) {
                        return '<a target="_blank" href="' + row.ficha_tecnica + '">' + row.producto + '</a>';
                    }, targets: 2
                },
                {render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 5
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.precio_unitario, 2, '.', ',');
                    }, targets: 6 //Precio soles
                },
                {render: function (data, type, row) {
                        if (row.precio_dolares == null)
                        {
                            return '$ ' + Util.formatoNumero(row.precio_unitario / tcUsd, 2, '.', ',');
                        } else
                        {
                            return '$ ' + Util.formatoNumero(row.precio_dolares, 2, '.', ',');
                        }
                    }, targets: 7 //Precio dólares
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.costo_envio, 2, '.', ',');
                    }, targets: 8 //Costo envío
                },
                {render: function (data, type, row) {
                        if (row.subtotal == null)
                        {
                            return '$ ' + Util.formatoNumero(((row.precio_unitario / tcUsd) * row.cantidad)+(row.cantidad*(row.costo_envio/tcUsd)), 2, '.', ',');
                        } else
                        {
                            return '$ ' + Util.formatoNumero(row.subtotal, 2, '.', ',');
                        }
                    }, targets: 9 //Subtotal
                }
            ],
            buttons: [
                /*{
                    text: '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar',
                    action: function () {
                        $('#formDetallesMarca').submit();
                    },
                    className: 'btn-sm'
                }*/
            ]
        });

        $tableDetalles.on('search.dt', function () {
            $('#tableDetallesMarca_filter input').attr('disabled', true);
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
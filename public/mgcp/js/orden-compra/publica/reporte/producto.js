var ReporteProducto = (function () {

    var token;

    function init(_token)
    {
        token = _token;
    }

    function contarFiltros()
    {
        $('#spanFiltrosProductos').html($('#modalFiltrosProductos').find('input[type=checkbox]:checked').length);
    }

    function actualizarFiltros(ruta)
    {
        contarFiltros();
        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: $('#modalFiltrosProductos').find('form').serialize(),
            error: function () {
                alert("Hubo un problema al aplicar los filtros. Actualice la página y vuelva a intentarlo.");
            }
        });
    }

    function resumen(ruta)
    {
        var $tableResumen=$('#tableProductos').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableProductos_filter');
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
                $('#tableProductos_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableProductos_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[7, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {_token: token},
                complete: function () {
                    ReporteProducto.contarFiltros();
                }
            },
            columns: [
                {data: 'catalogo'},
                {data: 'categoria'},
                {data: 'marca'},
                {data: 'modelo'},
                {data: 'part_no'},
                {data: 'ctd_oc', searchable: false},
                {data: 'cantidad', searchable: false},
                {data: 'monto', searchable: false}
            ],
            columnDefs: [
                {className: "text-center", targets: [2, 3, 4, 5, 6]},
                {className: "text-right", targets: [7]},
                {render: function (data, type, row) {
                        return '<a href="#" title="Ver datos adicionales de producto" data-toggle="modal" data-target="#modalDatosProducto" data-marca="' + row.marca + '" data-modelo="' + row.modelo + '" data-nroparte="' + row.part_no + '" class="producto" >' + row.part_no + '</a>';
                    }, targets: 4
                },
                {render: function (data, type, row) {
                        return '<a href="#" data-marca="' + row.marca + '" data-modelo="' + row.modelo + '" data-nroparte="' + row.part_no + '" data-toggle="modal" data-target="#modalDetallesProducto" class="reporteDetalles">' + Util.formatoNumero(row.ctd_oc, 0, '.', ',') + '</a>';
                    }, targets: 5
                },
                {render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 6
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.monto, 2, '.', ',');
                    }, targets: 7
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanFiltrosProductos">0</span>',
                    action: function () {
                        $('#modalFiltrosProductos').modal('show');
                    }
                }
            ]
        });
        $tableResumen.on('search.dt', function () {
            $('#tableProductos_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }
    
    function detalles($elemento, ruta, tcUsd)
    {
        var $modal = $('#modalDetallesProducto');
        $modal.find('span.nro-parte').html($elemento.closest('tr').find('td:eq(4)').find('a').html());
        $modal.find('span.marca').html($elemento.closest('tr').find('td:eq(2)').html());
        

        var $tableDetalles=$('#tableDetallesProducto').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableDetallesProducto_filter');
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
                $('#tableDetallesProducto_filter input').attr('disabled', false);
                $('#btnDetallesBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableDetallesProducto_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[4, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {marca: $elemento.data('marca'),modelo: $elemento.data('modelo'),nro_parte: $elemento.data('nroparte'), _token: token},
                complete: function () {
                    ReporteProducto.contarFiltros();
                }
            },
            columns: [
                {data: 'fecha_formalizacion', searchable: false},
                {data: 'nombre_entidad'},
                {data: 'orden_compra'},
                {data: 'razon_social'},
                {data: 'cantidad', searchable: false},
                {data: 'precio_unitario', searchable: false},
                {data: 'precio_dolares', searchable: false},
                {data: 'costo_envio', searchable: false},
                {data: 'subtotal', searchable: false},
                {data: 'plazo_entrega', searchable: false}
            ],
            columnDefs: [
                {orderable: false, targets: [6, 8]},
                {className: "text-center", targets: [0, 2, 4, 9]},
                {className: "text-right", targets: [5, 6, 7, 8]},
                {render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 4
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.precio_unitario, 2, '.', ',');
                    }, targets: 5 //precio pen
                },
                {render: function (data, type, row) {
                        if (row.precio_dolares==null)
                        {
                            return '$ ' + Util.formatoNumero((row.precio_unitario/tcUsd), 2, '.', ',');
                        }
                        else
                        {
                            return '$ ' + Util.formatoNumero(row.precio_dolares, 2, '.', ',');
                        }
                    }, targets: 6 //precio usd
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.costo_envio, 2, '.', ',');
                    }, targets: 7 //costo envio
                },
                {render: function (data, type, row) {
                    if (row.subtotal == null)
                    {
                        return '$ ' + Util.formatoNumero(((row.precio_unitario / tcUsd) * row.cantidad)+(row.cantidad*(row.costo_envio/tcUsd)), 2, '.', ',');
                    } else
                    {
                        return '$ ' + Util.formatoNumero(row.subtotal, 2, '.', ',');
                    }
                    }, targets: 8 //subtotal
                }
            ],
            buttons: [
            ]
        });

        $tableDetalles.on('search.dt', function () {
            $('#ttableDetallesProducto_filter input').attr('disabled', true);
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
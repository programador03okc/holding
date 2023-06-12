var ReporteProveedor = (function () {

    var token;

    function init(_token)
    {
        token = _token;
    }

    function contarFiltros()
    {
        $('#spanFiltrosProveedores').html($('#modalFiltrosProveedores').find('input[type=checkbox]:checked').length);
    }

    function actualizarFiltros(ruta)
    {
        ReporteProveedor.contarFiltros();
        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: $('#modalFiltrosProveedores').find('form').serialize(),
            error: function () {
                alert("Error: No se pudieron aplicar los filtros. Actualice la página y vuelva a intentarlo.");
            }
        });
    }

    function resumen(ruta)
    {
        var $tableResumen=$('#tableProveedores').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableProveedores_filter');
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
                $('#tableProveedores_filter input').attr('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableProveedores_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[6, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {_token: token},
                complete: function () {
                    ReporteProveedor.contarFiltros();
                }
            },
            columns: [
                {data: 'razon_social'},
                {data: 'catalogo'},
                {data: 'categoria'},
                {data: 'marca'},
                {data: 'ctd_oc', searchable: false},
                {data: 'cantidad', searchable: false},
                {data: 'monto', searchable: false}
            ],
            columnDefs: [
                {className: "text-center", targets: [3, 4, 5]},
                {className: "text-right", targets: [6]},
                {render: function (data, type, row) {
                        return '<a data-ruc="' + row.ruc_proveedor + '" data-categoria="' + row.categoria + '" data-marca="' + row.marca + '" href="#" data-toggle="modal" data-target="#modalDetallesProveedor" class="reporteDetalles">' + Util.formatoNumero(row.ctd_oc, 0, '.', ',') + '</a>';
                    }, targets: 4
                },
                {render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 5
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.monto, 2, '.', ',');
                    }, targets: 6
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanFiltrosProveedores">0</span>',
                    action: function () {
                        $('#modalFiltrosProveedores').modal('show');
                    }
                }
            ]
        });

        
        $tableResumen.on('search.dt', function () {
            $('#tableProveedores_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }

    function detalles($elemento, ruta, tcUsd)
    {
        var $modal = $('#modalDetallesProveedor');
        $modal.find('span.proveedor').html($elemento.closest('tr').find('td:eq(0)').html());
        $modal.find('span.categoria').html($elemento.data('categoria'));
        $modal.find('span.marca').html($elemento.data('marca'));

        var $tableDetalles=$('#tableDetallesProveedor').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                var $filter = $('#tableDetallesProveedor_filter');
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
                $('#tableDetallesProveedor_filter input').attr('disabled', false);
                $('#btnDetallesBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableDetallesProveedor_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[4, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {ruc: $elemento.data('ruc'), categoria: $elemento.data('categoria'), marca: $elemento.data('marca'), _token: token},
                complete: function () {
                    ReporteProveedor.contarFiltros();
                }
            },
            columns: [
                {data: 'fecha_formalizacion', searchable: false},
                {data: 'producto'},
                {data: 'orden_compra'},
                {data: 'nombre_entidad'},
                {data: 'cantidad', searchable: false},
                {data: 'precio_unitario', searchable: false},
                {data: 'precio_dolares', searchable: false},
                {data: 'costo_envio', searchable: false},
                {data: 'subtotal', searchable: false},
                {data: 'plazo_entrega', searchable: false}
            ],
            columnDefs: [
                {orderable: false, targets: [6,8]},
                {className: "text-center", targets: [0, 2, 4, 5, 9]},
                {className: "text-right", targets: [6, 7, 8]},
                {render: function (data, type, row) {
                        return '<a target="_blank" href="' + row.ficha_tecnica + '">' + row.producto + '</a>';
                    }, targets: 1
                },
                {render: function (data, type, row) {
                        return Util.formatoNumero(row.cantidad, 0, '.', ',');
                    }, targets: 4
                },
                {render: function (data, type, row) {
                        return 'S/ ' + Util.formatoNumero(row.precio_unitario, 2, '.', ',');
                    }, targets: 5 //precio pen
                },
                {render: function (data, type, row) {
                    if (row.precio_dolares == null)
                    {
                        return '$ ' + Util.formatoNumero(row.precio_unitario / tcUsd, 2, '.', ',');
                    } else
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
            $('#tableDetallesProveedor_filter input').attr('disabled', true);
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
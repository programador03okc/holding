var ReporteDepartamento = (function () {

    var token;

    function init(_token)
    {
        token = _token;
    }

    function contarFiltros()
    {
        $('#spanFiltrosDepartamentos').html($('#modalFiltrosDepartamentos').find('input[type=checkbox]:checked').length);
    }

    function actualizarFiltros(ruta)
    {
        ReporteDepartamento.contarFiltros();
        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: $('#modalFiltrosDepartamentos').find('form').serialize(),
            error: function () {
                alert("Hubo un problema al aplicar los filtros. Actualice la página y vuelva a intentarlo.");
            }
        });
    }

    function resumen(ruta)
    {
        var $tableDepartamentos = $('#tableDepartamentos').DataTable({
            pageLength: 50,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                $('#tableDepartamentos_filter').append('<button id="btnDepartamentoBuscar" class="btn btn-default mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $('#tableDepartamentos_filter input').unbind();
                $('#tableDepartamentos_filter input').bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $tableDepartamentos.search(this.value).draw();
                    }
                });
                $('#btnDepartamentoBuscar').click(function () {
                    $tableDepartamentos.search($('#tableDepartamentos_filter input').val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableDepartamentos_filter input').attr('disabled', false);
                $('#btnDepartamentoBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[0, "asc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {_token: token},
                complete: function () {
                    ReporteDepartamento.contarFiltros();

                }
            },
            columns: [
                {data: 0, name: 'razon_social'},
                {data: 1, name: 'catalogo'},
                {data: 2, name: 'categoria'},
                {data: 3, name: 'departamento'},
                {data: 4, name: 'ctd_oc'},
                {data: 5, name: 'promedio_plazo'}
            ],
            columnDefs: [
                {className: "text-center", targets: [3, 4, 5]},
                {render: function (data, type, row) {
                        return '<a data-proveedor="' + row[0] + '" data-categoria="' + row[2] + '" data-departamento="' + row[3] + '" href="#" data-toggle="modal" data-target="#modalDetallesDepartamento" class="reporteDetalles">' + util.formatoNumero(row[4], 0, '.', ',') + '</a>';
                    }, targets: 4
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Filtros: <span id="spanFiltrosDepartamentos">0</span>',
                    action: function () {
                        $('#modalFiltrosDepartamentos').modal('show');
                    }
                }
            ]
        });
    }

    function detalles($elemento, ruta, tcUsd)
    {
        var $modal = $('#modalDetallesDepartamento');
        $modal.find('span.proveedor').html($elemento.data('proveedor'));
        $modal.find('span.categoria').html($elemento.data('categoria'));
        $modal.find('span.departamento').html($elemento.data('departamento'));
        $('#tableDetallesDepartamento').DataTable().destroy();

        $('#tableDetallesDepartamento').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[4, "desc"]],
            ajax: {
                url: ruta,
                type: "POST",
                data: {proveedor: $elemento.data('proveedor'), categoria: $elemento.data('categoria'), departamento: $elemento.data('departamento'), _token: token},
                complete: function () {
                    ReporteDepartamento.contarFiltros();
                }
            },
            columns: [
                {data: 0, name: 'fecha_formalizacion'},
                {data: 2, name: 'producto'},
                {data: 3, name: 'orden_compra'},
                {data: 1, name: 'entidad'},
                {data: 5, name: 'cantidad'},
                {data: 6, name: 'precio_unitario'},
                {data: 7, name: 'precio_dolares'},
                {data: 8, name: 'costo_envio'},
                {data: 9, name: 'subtotal'},
                {data: 10, name: 'plazo_entrega'}
            ],
            columnDefs: [
                {orderable: false, targets: [6,8]},
                {className: "text-center", targets: [0, 2, 4, 5, 9]},
                {className: "text-right", targets: [6, 7, 8]},
                {render: function (data, type, row) {
                        return '<a target="_blank" href="' + row[11] + '">' + row[2] + '</a>';
                    }, targets: 1
                },
                {render: function (data, type, row) {
                        return util.formatoNumero(row[5], 0, '.', ',');
                    }, targets: 4
                },
                {render: function (data, type, row) {
                        return 'S/ ' + util.formatoNumero(row[6], 2, '.', ',');
                    }, targets: 5 //precio pen
                },
                {render: function (data, type, row) {
                        if (row[7]==null)
                        {
                            return '$ ' + util.formatoNumero((row[6]/tcUsd), 2, '.', ',');
                        }
                        else
                        {
                            return '$ ' + util.formatoNumero(row[7], 2, '.', ',');
                        }
                    }, targets: 6 //precio usd
                },
                {render: function (data, type, row) {
                        return 'S/ ' + util.formatoNumero(row[8], 2, '.', ',');
                    }, targets: 7 //costo envio
                },
                {render: function (data, type, row) {
                        return '$ ' + util.formatoNumero(((row[6]/tcUsd)*row[5])+((row[8]/tcUsd)*row[5]), 2, '.', ',');
                    }, targets: 8 //subtotal
                }

            ],
            buttons: [
            ]
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
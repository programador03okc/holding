class OrdenCompraPublicaView {
    constructor(model) {
        this.model = model;
    }

    listar=()=>{
        const model=this.model;
        const $tableOrdenes = $('#tableOrdenes').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                /*const $filter = $('#tableOrdenes_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').click(function () {
                    $tableOrdenes.search($input.val()).draw();
                });*/
                const $filter = $('#tableOrdenes_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableOrdenes.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableOrdenes_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
                $('#tableOrdenes_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[2, "desc"]],
            ajax: {
                url: route('mgcp.ordenes-compra.publicas.data-lista'),
                type: "POST",
                data: {_token: model.token},
                
            },
            columns: [
                {data: 'orden_compra', name:'oc_publicas.orden_compra', className:'text-center', orderable:false},
                {data: 'fecha_formalizacion', searchable: false, className:'text-center'},
                {data: 'nombre_entidad', name:'entidades.nombre'},
                {data: 'razon_social', name: 'oc_publicas.razon_social'},
                {data: 'categoria', name: 'categorias.descripcion', searchable: false, className:'text-center'},
                {data: 'marca', name: 'productos_am.marca'},
                {data: 'part_no', name: 'productos_am.part_no', className:'text-center'},
                {data: 'cantidad', searchable: false, className:'text-center'},
                {data: 'precio_unitario', searchable: false, className:'text-right'},
                {data: 'precio_unitario', searchable: false, className:'text-right'},
                {data: 'costo_envio', searchable: false, className:'text-right'},
                {data: 'plazo_entrega', searchable: false, className:'text-center', orderable:false},
                {data: 'provincia', name: 'provincias.nombre', searchable: false},
                {data: 'modelo', name: 'productos_am.modelo'}
            ],
            columnDefs: [
                {orderable: false, targets: [13]},
                {className: "text-center", targets: [13]},
                {render: function (data, type, row) {
                        return `<a title="Ver detalles" target="_blank" href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row.orden_compra_publica.id}&ImprimirCompleto=1">${row.orden_compra_publica.orden_compra}</a>`;//'<a title="Ver estados" data-id="' + row.orden_compra_publica.id + '" data-orden="' + row.orden_compra_publica.orden_compra + '" href="#" class="estadoPc">' + row.orden_compra_publica.orden_compra + '</a>';
                    }, targets: 0
                },
                {render: function (data, type, row) {
                        return row.orden_compra_publica.fecha_formalizacion_format;
                    }, targets: 1
                },
                {render: function (data, type, row) {
                        //return '<a title="Ver información de entidad" href="#" class="entidad">' + row.orden_compra_publica.entidad.nombre + '</a>';
                        return `${row.orden_compra_publica.entidad.semaforo} <a title="Ver información de entidad" href="#" class="entidad" data-id="${row.id_entidad}">${row.nombre_entidad}</a>`;
                    }, targets: 2
                },
                {render: function (data, type, row) {
                        return '<span class="producto">' + row.marca + ' ' + row.modelo + '</span>';
                    }, targets: 5
                },
                {render: function (data, type, row) {
                        return '<a title="Ver datos adicionales de producto" data-toggle="modal" data-target="#modalDatosProducto" href="#" data-id="' + row.producto.id + '" class="producto">' + row.part_no + '</a>';
                    }, targets: 6
                },
                {render: function (data, type, row) {
                        return row.precio_unitario_format;
                    }, targets: 8
                },
                {render: function (data, type, row) {
                        return row.precio_unitario_usd_format;
                    }, targets: 9
                },
                {render: function (data, type, row) {
                        return 'S/' + Util.formatoNumero(row.costo_envio, 2);
                    }, targets: 10
                },
                {render: function (data, type, row) {
                        let botones = `
                        <div class="btn-group" role="group">
                            <button title="Ver descuentos por volumen" disabled data-toggle="modal" data-target="#modalDctoVolumen" data-empresa="0" data-producto="${row.producto.id}" class="btn btn-default btn-xs dctoVolumen"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span></button>
                            <button title="Ver historial de actualizaciones de producto" data-toggle="modal" data-target="#modalHistorialActualizaciones" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}" 
                            data-empresa="0" data-producto="${row.producto.id}" class="btn btn-default btn-xs historial-actualizaciones"><span class="fa fa-history" aria-hidden="true"></span></button>
                            <br>
                            <button title="Actualizar stock de producto en Perú Compras" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}" data-toggle="modal" data-target="#modalActualizarDataPortal" data-tipo="stock" data-producto="${row.producto.id}" class="btn btn-default btn-xs actualizar-stock-precio"><span class="fa fa-cube" aria-hidden="true"></span></button>
                            <button title="Actualizar precio de producto en Perú Compras" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}" data-toggle="modal" data-target="#modalActualizarDataPortal" data-tipo="precio" data-producto="${row.producto.id}" class="btn btn-default btn-xs actualizar-stock-precio"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></button>
                        </div>`;
                        return botones;
                    }, targets: 13
                }
            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                    action: function () {
                        $('#modalFiltros').modal('show');
                    },
                    className: 'btn btn-sm'
                }
            ]
        });

        $tableOrdenes.on('search.dt', function () {
            $('#tableOrdenes_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });

        $tableOrdenes.on('processing.dt', function (e, settings, processing) {
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

    obtenerEstadosPortalEvent=()=> {
        $('tbody').on("click", "a.estados-oc-portal", (e)=> {
            e.preventDefault();
            const $elemento=$(e.currentTarget);
            const $modal = $('#modalEstadosOcPortal');
            $modal.find('span.orden-compra').html($elemento.data('oc'));
            $modal.find('div.modal-body').html('<div class="text-center">Obteniendo datos de Perú Compras...</div>');
            $modal.modal('show');
            this.model.obtenerEstadosPortal($elemento.data('id')).then((datos)=>{
                $modal.find('div.modal-body').html(datos);
            }).fail(()=>{
                $modal.find('div.modal-body').html("Hubo un problema al obtener los estados. Por favor actualice la página y vuelva a intentarlo");
            });
        });
    }

    verOfertasPorMMNEvent = () => {
        //Ver ofertas del producto en O/C públicas
        $('#btnOcultarOcSinFecha').click((e) => {
            this.obtenerOrdenesPorMMN(e);
            if ($(e.currentTarget).data('ocultar') == '1') {
                $(e.currentTarget).data('ocultar', '0').html('Mostrar órdenes sin fecha');
            } else {
                $(e.currentTarget).data('ocultar', '1').html('Ocultar órdenes sin fecha');
            }
        });

        $('body').on("click", ".ver-ofertas-oc", (e) => {
            e.preventDefault();
            let $boton = $('#btnOcultarOcSinFecha');
            $boton.data('marca', $(e.currentTarget).data('marca'));
            $boton.data('modelo', $(e.currentTarget).data('modelo'));
            $boton.data('nroparte', $(e.currentTarget).data('nroparte'));
            $boton.data('ocultar', '1')
            $boton.html('Ocultar órdenes sin fecha');
            this.obtenerOrdenesPorMMN(e);
        });
    }

    obtenerOrdenesPorMMN = (e) => {
        const model = this.model;
        const $elemento = $(e.currentTarget);
        e.preventDefault();
        $('#btnOcultarOcSinFecha').prop('disabled', true);
        $('#modalOfertasOc').find('span.producto').html($elemento.data('marca') + ' ' + $elemento.data('modelo') + ' ' + $elemento.data('nroparte'));
        if ($.fn.DataTable.isDataTable('#tableOrdenesCompra')) {
            $('#tableOrdenesCompra').DataTable().destroy();
            $('#tableOrdenesCompra').find('tbody').html('');
        }

        let $tableOc = $('#tableOrdenesCompra').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableOrdenesCompra_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnTableOcBuscar" class="btn btn-primary btn-sm mr-xs pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.unbind();
                $input.bind('keyup', function (e) {
                    if (e.keyCode == 13) {
                        $('#btnTableOcBuscar').trigger('click');
                    }
                });
                $('#btnTableOcBuscar').click(function () {
                    $tableOc.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableOrdenesCompra_filter input').attr('disabled', false);
                $('#btnTableOcBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
                $('#tableOrdenesCompra_filter input').focus();
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[0, "desc"]],
            ajax: {
                url: route('mgcp.ordenes-compra.publicas.obtener-ordenes-por-producto'),
                type: "POST",
                data: { marca: $elemento.data('marca'), modelo: $elemento.data('modelo'), nroParte: $elemento.data('nroparte'), _token: model.token },
                /*complete: function () {
                    $('#btnOcultarOcSinFecha').prop('disabled', false);
                }*/
            },
            columns: [
                { data: 'fecha_formalizacion', searchable: false },
                { data: 'razon_social', name: 'oc_publicas.razon_social' },
                { data: 'nombre_entidad', name: 'entidades.nombre' },
                { data: 'nombre_departamento', name: 'departamentos.nombre' },
                { data: 'cantidad', className: 'text-center', searchable: false },
                { data: 'precio_unitario', searchable: false, className: 'text-right' },
                { data: 'precio_unitario', searchable: false, className: 'text-right', orderable: false },
                { data: 'id_orden_compra', searchable: false, className: 'text-center', orderable: false },
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return row.orden_compra_publica.fecha_formalizacion_format;
                    }, targets: 0
                },
                {
                    render: function (data, type, row) {
                        return row.precio_unitario_format;
                    }, targets: 5
                },
                {
                    render: function (data, type, row) {
                        return row.precio_unitario_usd_format;
                    }, targets: 6
                },
                {render: function (data, type, row) {
                        return `<a class="btn btn-default btn-xs" title="Descargar O/C" target="_blank" href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${row.id_orden_compra}&ImprimirCompleto=1"><span class="fa fa-file-pdf-o"></span></a>`;
                    }, targets: 7
                },
            ],
            buttons: [
            ]
        });

        $tableOc.on('search.dt', function () {
            $('#tableOrdenesCompra_filter input').attr('disabled', true);
            $('#btnTableOcBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').attr('disabled', true);
        });
    }
}
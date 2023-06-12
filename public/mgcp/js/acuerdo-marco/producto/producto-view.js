class ProductoView {
    constructor(model) {
        this.model = model;
        // this.empresa = empresa;
    }

    listar = (empresa) => {
        const model = this.model;
        const $tableProductos = $('#tableProductos').DataTable({
            search: {
                smart: false
            },
            pageLength: 50,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableProductos_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableProductos.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableProductos_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
                $('#tableProductos_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [[1, "asc"]],
            ajax: {
                url: route('mgcp.acuerdo-marco.productos.data-lista'),
                type: "POST",
                data: function ( params ) {
                    return Object.assign(params, Util.objectifyForm($('#formFiltros').serializeArray()))
                },
            },
            columns: [
                { data: 'acuerdo_marco', name: 'mgcp_acuerdo_marco.acuerdo_marco.descripcion', searchable: false, className: 'text-center' },
                { data: 'descripcion', searchable: false },
                { data: 'marca', searchable: false, className: 'text-center' },
                { data: 'modelo', searchable: false, className: 'text-center' },
                { data: 'part_no', searchable: false, className: 'text-center' },
                { data: 'descontinuado', searchable: false, className: 'text-center', orderable: false }
            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        return row.descripcion;
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        let $precio = '';
                        switch (empresa) {
                            case '1':
                                if (row.puntaje_okc >= 20) {
                                    $precio = '<span class="verde">' + row.precio_okc + '</span>';
                                } else
                                    if (row.puntaje_okc < 20) {
                                        $precio = '<span class="rojo">' + row.precio_okc + '</span>';
                                    } else {
                                        $precio = "-";
                                }
                            break;

                            case '2':
                                if (row.puntaje_proy >= 20) {
                                    $precio = '<span class="verde">' + row.precio_proy + '</span>';
                                } else
                                    if (row.puntaje_proy < 20) {
                                        $precio = '<span class="rojo">' + row.precio_proy + '</span>';
                                    } else {
                                        $precio = "-";
                                }
                            break;

                            case '3':
                                if (row.puntaje_smart >= 20) {
                                    $precio = '<span class="verde">' + row.precio_smart + '</span>';
                                } else
                                    if (row.puntaje_smart < 20) {
                                        $precio = '<span class="rojo">' + row.precio_smart + '</span>';
                                    } else {
                                        $precio = "-";
                                }
                            break;

                            case '4':
                                if (row.puntaje_deza >= 20) {
                                    $precio = '<span class="verde">' + row.precio_deza + '</span>';
                                } else
                                    if (row.puntaje_deza < 20) {
                                        $precio = '<span class="rojo">' + row.precio_deza + '</span>';
                                    } else {
                                        $precio = "-";
                                }
                            break;

                            case '5':
                                if (row.puntaje_dorado >= 20) {
                                    $precio = '<span class="verde">' + row.precio_dorado + '</span>';
                                } else
                                    if (row.puntaje_dorado < 20) {
                                        $precio = '<span class="rojo">' + row.precio_dorado + '</span>';
                                    } else {
                                        $precio = "-";
                                }
                            break;

                            case '6':
                                if (row.puntaje_protec >= 20) {
                                    $precio = '<span class="verde">' + row.precio_protec + '</span>';
                                } else
                                    if (row.puntaje_protec < 20) {
                                        $precio = '<span class="rojo">' + row.precio_protec + '</span>';
                                    } else {
                                        $precio = "-";
                                }
                            break;
                        }
                        return $precio;
                    }, targets: 5
                },
                {
                    render: function (data, type, row) {
                        let botones = `
                        <div class="btn-group" role="group">
                            <a title="Ver foto" target="_blank" href="${row.imagen}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></button>
                            <a title="Descargar ficha técnica" target="_blank" href="${row.ficha_tecnica}" class="btn btn-default btn-xs"><span class="fa fa-file-pdf-o" aria-hidden="true"></span></a>
                            <button data-toggle="modal" data-target="#modalOfertasOc" data-marca="${row.marca}" data-modelo="${row.modelo}" data-nroparte="${row.part_no}" 
                                title="Ver ofertas de este producto en órdenes de compra públicas" class="btn btn-default btn-xs ver-ofertas-oc"><span class="fa fa-bar-chart-o" aria-hidden="true"></span>
                            </button>
                            <br>
                            <button title="Ver historial de actualizaciones de producto" data-toggle="modal" data-target="#modalHistorialActualizaciones" data-marca="${row.marca}" data-modelo="${row.modelo}" data-nroparte="${row.part_no}" 
                                data-empresa="0" data-producto="${row.id}" data-empresa="0" data-producto="${row.id}" class="btn btn-default btn-xs historial-actualizaciones"><span class="fa fa-history" aria-hidden="true"></span>
                            </button>
                            <button title="Actualizar stock de producto en Perú Compras" data-toggle="modal" data-target="#modalActualizarDataPortal" data-marca="${row.marca}" data-modelo="${row.modelo}" data-nroparte="${row.part_no}" data-tipo="stock" data-producto="${row.id}" class="btn btn-default btn-xs actualizar-stock-precio"><span class="fa fa-cube" aria-hidden="true"></span>
                            </button>
                            <button title="Actualizar precio de producto en Perú Compras" data-toggle="modal" data-target="#modalActualizarDataPortal" data-marca="${row.marca}" data-modelo="${row.modelo}" data-nroparte="${row.part_no}" data-tipo="precio" data-producto="${row.id}" class="btn btn-default btn-xs actualizar-stock-precio"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span>
                            </button>
                        </div>`;
                        return botones;

                    }, targets: 6
                },
                {
                    render: function (data, type, row) {
                        var checked = '';
                        if (row.descontinuado == true) {
                            checked = 'checked';
                        }
                        return `<input type="checkbox" name="descontinuado_${row.id}" class="input-check" data-producto="${row.id}" ${checked}>`;
                    }, targets: 7
                },
            ],

            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                    action: function () {
                        $('#modalFiltros').modal('show');
                    }, className: 'btn-sm'
                }
            ]
        });

        $tableProductos.on('search.dt', function () {
            $('#tableProductos_filter input').attr('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
        });

        $tableProductos.on('processing.dt', function (e, settings, processing) {
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

    obtenerDetallesEvent = () => {
        //Detalles de producto por ID
        $('body').on("click", "a.producto", (e) => {
            e.preventDefault();
            const $modal=$('#modalDatosProducto');
            const $mensaje=$modal.find('div.mensaje');
            const $contenido = $modal.find('div.contenido');
            Util.bloquearConSpinner($mensaje);
            $contenido.html('');
            this.model.obtenerDetallesPorId($(e.currentTarget).data('id')).then((datos) => {
                $contenido.html(`<div class="text-justify">${datos.descripcion}</div><br>
                                <div class="btn-group" role="group">
                                <a href="${datos.imagen}" target="_blank" class="btn btn-default"><span class="glyphicon glyphicon-picture"></span> Imagen</a>
                                <a href="${datos.ficha_tecnica}" target="_blank" class="btn btn-default"><span class="fa fa-file-pdf-o"></span> Ficha</a>
                                </div>`);
                                Util.liberarBloqueoSpinner($mensaje);
            }).fail(() => {
                alert("Hubo un problema al obtener los datos del producto. Por favor actualice la página e intente de nuevo");
                $modal.modal('hide');
            });
        });
    }

    obtenerPrecioStockPortalEvent = () => {
        //Obtener precio o stock del portal
        $('tbody').on("click", ".actualizar-stock-precio", (e) => {
            e.preventDefault();
            const $elemento = $(e.currentTarget);
            const $modal = $('#modalActualizarStockPrecio');
            $modal.modal('show');
            $modal.find('span.producto').html($elemento.data('marca') + ' ' + $elemento.data('modelo') + ' ' + $elemento.data('nroparte'));
            $modal.find('span.tipo').html($elemento.data('tipo'));
            const $tbody = $modal.find('tbody');
            $tbody.find('button, input, textarea').prop('disabled', true);
            $tbody.find('input, textarea').val('');
            $tbody.find('td.resultado').html('');

            $tbody.find('tr').each((index, element) => {
                let $fila = $(element);
                $fila.data('producto', $elemento.data('producto'));
                $fila.data('tipo', $elemento.data('tipo'));
                $fila.find('td.valor-actual').html('Obteniendo datos del portal...');
                this.model.obtenerPrecioStockPortal($elemento.data('tipo'), $fila.data('empresa'), $elemento.data('producto')).then((dato) => {
                    if (dato.tipo == 'success') {
                        $tbody.find('tr.' + dato.empresa).find('button, input, textarea').prop('disabled', false);
                    }
                    $tbody.find('tr.' + dato.empresa).find('td.valor-actual').html(dato.valor);
                }).fail(() => {
                    $fila.find('td.valor-actual').html('<span class="text-danger">Hubo un problema al conectar. Por favor vuelva a intentarlo</span>');
                });
            });
        });

        $('#modalActualizarStockPrecio').on("click", "button.actualizar", (e) => {
            const $tbody = $('#modalActualizarDataPortal').find('tbody');
            const $fila = $(e.currentTarget).closest('tr');
            const $resultado = $fila.find('td.resultado');
            $fila.find('button, input, textarea').prop('disabled', true);
            $resultado.html('Actualizando...');

            this.model.actualizarPrecioStockPortal($fila.data('producto'), $fila.data('empresa'), $fila.data('tipo'), $fila.find('td.valor-actual').html(), $fila.find('input[name=valor]').val(), $fila.find('textarea').val()).then((dato) => {
                $fila.find('td.resultado').html('<span class="text-' + dato.tipo + '">' + dato.mensaje + '</span>');
                if (dato.tipo == 'success') {
                    $('#tableProductos').DataTable().ajax.reload(null,false);
                }
            }).fail(() => {
                $resultado.html('<span class="text-danger">Hubo un problema al actualizar. Por favor vuelva a intentarlo</span>');
            }).always(() => {
                $fila.find('input, textarea, button').prop('disabled', false);
            });
        });
    }

    estadoProductoStock = () => {
        $('tbody').on("change", ".input-check", (e) => {
            e.preventDefault();
            const $elemento = $(e.currentTarget);
            var pregunta = '';
            var $estado = false;
            var $producto = $elemento.data('producto');

            if ($elemento.is(':checked') ) {
                pregunta = "¿Desea considerar el producto como descontinuado?";
                $elemento.prop('checked', true);
                $estado = true;
            } else {
                pregunta = "¿Desea considerar el producto como continuado para actualizar stock?";
                $elemento.prop('checked', false);
            }

            if (confirm(pregunta)) {
                this.model.actualizarEstadoStock($producto, $estado).then((data) => {
                    console.log(data);
                }).fail(() => {
                    Util.notify('error', 'No se pudo seleccionar la MPG, volver a intentarlo');
                });
            }
        });
    }
}


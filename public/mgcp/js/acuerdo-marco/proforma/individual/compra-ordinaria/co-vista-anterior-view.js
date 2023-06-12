import ProformaIndividualView from "../proforma-individual-view.js";

export default class COVistaAnteriorView extends ProformaIndividualView {

    constructor(model, idUsuario) {
        super(model, idUsuario)
    }

    listarProformas = (puedeVerPrecios, puedeDeshacerCotizaciones) => {
        //const proforma = this.model;
        const idUsuario = this.idUsuario;
        const $tableProformas = $('#tableProformas').DataTable({
            search: {
                smart: false
            },
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableProformas_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
                $input.off();
                $input.on('keyup', (e) => {
                    if (e.key == 'Enter') {
                        $('#btnBuscar').trigger('click');
                    }
                });
                $('#btnBuscar').on('click', (e) => {
                    $tableProformas.search($input.val()).draw();
                });
            },
            drawCallback: function (settings) {
                $('#tableProformas_filter input').prop('disabled', false);
                $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').prop('disabled', false);
                $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
                $('#tableProformas_filter input').trigger('focus');
            },
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.vista-anterior.generar-lista-para-datatable'),
                type: "POST",
                data: function ( params ) {
                    return Object.assign(params, Util.objectifyForm($('#formFiltros').serializeArray()))
                }
            },
            columns: [
                { data: 'requerimiento' },
                { data: 'proforma' },
                { data: 'nombre_entidad', name: 'entidades.nombre' },
                { data: 'fecha_limite', searchable: false, className: 'text-center' },
                { data: 'categoria', name: 'categorias.descripcion' },
                { data: 'descripcion_producto', name: 'productos_am.descripcion' },
                { data: 'part_no', name: 'productos_am.part_no' },
                { data: 'fin_entrega', className: 'text-center' },
                { data: 'marca', name: 'productos_am.marca', className: 'text-center' }, //Botones de herramientas
                { data: 'nombre_empresa', name: 'empresas.empresa', className: 'text-center' },
                { data: 'lugar_entrega' },
                { data: 'precio_unitario_base', searchable: false, className: 'text-center' },
                { data: 'cantidad', searchable: false, className: 'text-center' },
                { data: 'estado', className: 'text-center' },
                { data: 'precio_publicar', searchable: false, className: 'text-center' },
                { data: 'costo_envio_publicar', searchable: false, className: 'text-center' },
                { data: 'name', name: 'users.name', visible: false },
                { data: 'ruc', name: 'entidades.ruc', visible: false }
            ],
            columnDefs: [
                { orderable: false, targets: [8] },
                {
                    targets: 13,
                    createdCell: function (td, cellData, rowData, row, col) {
                        td.setAttribute('class', 'estado text-center');
                    }
                },
                {
                    targets: 14,
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (rowData.estado == 'PENDIENTE') // || rowData.estado == 'COTIZADA'
                        {
                            td.setAttribute('contenteditable', 'true');
                            td.setAttribute('class', 'success decimal text-center precio');
                        } else {
                            td.setAttribute('class', 'decimal text-center precio');
                        }
                        td.setAttribute('data-id', rowData.nro_proforma);
                        td.setAttribute('data-campo', 'precio_publicar');
                    }
                },
                {
                    targets: 15,
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (rowData.requiere_flete == 1 && rowData.estado == 'PENDIENTE') // || rowData.estado == 'COTIZADA'
                        {
                            td.setAttribute('contenteditable', 'true');
                            td.setAttribute('class', 'success decimal flete text-center');
                        } else {
                            td.setAttribute('class', 'decimal text-center flete');
                        }
                        td.setAttribute('data-id', rowData.nro_proforma);
                        td.setAttribute('data-campo', 'costo_envio_publicar');
                    }
                },
                {
                    render: function (data, type, row) {
                        return `${row.entidad.semaforo} <a title="Ver información de entidad" href="#" class="entidad" data-id="${row.id_entidad}" data-ruc="${row.ruc}">${row.nombre_entidad}</a>`;
                    }, targets: 2
                },
                {
                    render: function (data, type, row) {
                        return `<div>${row.fecha_emision}</div><div>${row.fecha_limite}</div>`;
                    }, targets: 3
                },
                {
                    render: function (data, type, row) {
                        let producto = `<a title="Ver datos adicionales de producto" data-toggle="modal" data-target="#modalDatosProducto" href="#" data-id="${row.producto.id}" class="producto">
                            ${row.producto.marca} ${row.producto.modelo}</a>`;
                        return producto;
                    }, targets: 5
                },
                {
                    render: function (data, type, row) {
                        return `<span class="nro-parte">${row.producto.part_no}</span>`;
                    }, targets: 6
                },
                {
                    render: function (data, type, row) {
                        return `${row.inicio_entrega}<br>${row.fin_entrega}`;
                    }, targets: 7
                },
                {
                    render: function (data, type, row) {

                        let botones = `<div class="dropdown">
                        <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                          <span class="glyphicon glyphicon-th"></span> 
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="ver-ofertas-oc" data-toggle="modal" data-target="#modalOfertasOc" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}"  href="#"><span class="fa fa-bar-chart-o"></span>Ver precios en O/C públicas</a></li>
                          <!--<li><a class="historial-actualizaciones" data-toggle="modal" data-target="#modalHistorialActualizaciones" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}" data-empresa="${row.id_empresa}" data-producto="${row.producto.id}" href="#"><span class="fa fa-history"></span>Ver hist. de actualiz. del producto</a></li>
                          <li><a class="actualizar-stock-precio" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}" data-tipo="precio" data-producto="${row.producto.id}" href="#"><span class="glyphicon glyphicon-usd"></span>Actualizar precio en Perú Compras</a></li>
                          <li><a class="actualizar-stock-precio" data-marca="${row.producto.marca}" data-modelo="${row.producto.modelo}" data-nroparte="${row.producto.part_no}" data-tipo="stock" data-producto="${row.producto.id}" href="#"><span class="fa fa-cube"></span>Actualizar stock en Perú Compras</a></li>
                          <li><a class="comentarios" data-id="${row.nro_proforma}" data-proforma="${row.proforma}" href="#"><span class="glyphicon glyphicon-comment"></span>Comentarios en proforma</a></li>-->
                        </ul>
                      </div>`;
                        return botones;
                    }, targets: 8
                },
                {
                    render: function (data, type, row) {
                        return `${row.empresa.semaforo} ${row.empresa.empresa}`;
                    }, targets: 9
                },
                {
                    render: function (data, type, row) {
                        return `<div>${Util.formatoNumero(row.precio_unitario_base, 2)} ${row.moneda_ofertada}</div><div>${row.software_educativo}</div>`;
                    }, targets: 11
                },
                {
                    render: function (data, type, row) {
                        return row.cantidad;
                    }, targets: 12
                },
                {
                    render: function (data, type, row) {
                        let estado = row.estado;
                        if (row.nombre_corto != null) {
                            estado += `<div>(${row.nombre_corto})</div>`;
                        }
                        if (row.puede_deshacer_cotizacion == '1' && (idUsuario == row.id_usuario || puedeDeshacerCotizaciones == '1')) {
                            estado += `<div><a href="#" class="deshacer" data-id="${row.nro_proforma}">(Deshacer)</a></div>`;
                        }
                        return estado;
                    }, targets: 13
                },
                {
                    render: function (data, type, row) {
                        let precio = row.precio_publicar == null ? '' : Util.formatoNumero(row.precio_publicar, 2)
                        if (row.estado == 'COTIZADA' || row.estado == 'SELECCIONADA') {
                            if (puedeVerPrecios == '1' || row.id_usuario == idUsuario) {
                                return precio;
                            } else {
                                return '(Oculto)';
                            }

                        } else {
                            return precio;
                        }

                    }, targets: 14
                },
                {
                    render: function (data, type, row) {
                        if (row.requiere_flete == '0') {
                            return 'N/R';
                        }
                        else {
                            return row.costo_envio_publicar == null ? '' : Util.formatoNumero(row.costo_envio_publicar, 2);
                        }
                    }, targets: 15
                },

            ],
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                    action: function () {
                        $('#modalFiltros').modal('show');
                    }, className: 'btn-sm'
                },
                {
                    text: '<span class="glyphicon glyphicon-time" aria-hidden="true"></span> Ver última act. de lista', action: function () {
                        $('#modalUltimaActualizacionLista').modal('show');
                    }, className: 'btn-sm'
                },
                {
                    text: '<span class="fa fa-users" aria-hidden="true"></span> Ver fondos disp.', action: function () {
                        //obtenerListaActualizarPortal();
                        $('#modalFondosDisponibles').modal('show');
                    }, className: 'btn-sm'
                },
                {
                    text: '<span class="glyphicon glyphicon-usd" aria-hidden="true"></span> Ingresar flete por lote', action: function () {
                        $('#modalIngresarFletePorLote').modal('show');
                    }, className: 'btn-sm'
                },
                {
                    text: '<span class="fa fa-share-square-o" aria-hidden="true"></span> Enviar cotiz. a Perú Compras', action: function () {
                        //obtenerListaActualizarPortal();
                        $('#modalProformasEnviar').modal('show');
                    }, className: 'btn-sm'
                }
            ]
        });

        $tableProformas.on('search.dt', function () {
            $('#tableProformas_filter input').prop('disabled', true);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-time" aria-hidden="true"></span>').prop('disabled', true);
        });

        $tableProformas.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc",
                    zIndex: 5
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });
    }



    ingresarFletePorLoteEvent = () => {
        //Ingreso de flete masivo

        $('#btnIngresarFletePorLote').on('click', (e) => {
            const $flete = $('#txtFletePorLote');
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + ' Ingresando');

            this.model.ingresarFletePorLote($flete.val()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#tableProformas').DataTable().ajax.reload();
                    $('#modalIngresarFletePorLote').modal('hide');
                    $flete.val('');
                }
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Ingresar');
            }).fail(() => {
                Util.notify('error', 'Hubo un error al procesar el flete. Por favor actualice la página e intente de nuevo');
            });
        });
    }
}
class OrdenCompraPropiaView {
    constructor(model, permisos, idUsuario) {
        this.model = model;
        this.permisos = permisos;
        this.idUsuario = idUsuario;
        this.rutaCuadroCosto = route('mgcp.cuadro-costos.detalles');
    }

    listar = () => {
        const model = this.model;
        const $selectEtapas = $('#selectEtapas');
        const $selectCorporativos = $('#selectCorporativos');
        const rutaCuadroCosto = this.rutaCuadroCosto;
        const permisos = this.permisos;
        //const $selectUsuarios = $('#selectUsuarios');
        const $tableOrdenes = $('#tableOrdenes').DataTable({
            pageLength: 20,
            dom: 'Bfrtip',
            serverSide: true,
            initComplete: function (settings, json) {
                const $filter = $('#tableOrdenes_filter');
                const $input = $filter.find('input');
                $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
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
            order: [[4, "desc"]],
            ajax: {
                url: route('mgcp.ordenes-compra.propias.data-lista'),
                type: "POST",
                data: function ( params ) {
                    return Object.assign(params, Util.objectifyForm($('#formFiltros').serializeArray()))
                },
                complete: function () {
                    Util.activarDatePicker();
                }
            },
            columns: [
                { data: 'nro_orden', className: 'text-center' },
                { data: 'descripcion_corta_am', className: 'text-center' },
                { data: 'nombre_entidad',  className: 'text-center' },
                { data: 'fecha_publicacion', className: 'text-center' },
                { data: 'estado_oc', className: 'text-center' },
                { data: 'fecha_estado', searchable: false, className: 'text-center' },
                { data: 'estado_entrega' },
                { data: 'fecha_entrega', searchable: false, className: 'text-center' },
                { data: 'monto_total', className: 'text-right' },
                { data: 'orden_compra', className: 'text-center' },
                { data: 'factura', className: 'text-center' },
                { data: 'guia', className: 'text-center' },
                { data: 'etapa', className: 'text-center' },
                { data: 'nombre_largo_responsable', className: 'text-center' },
                { data: 'codigo_oportunidad', className: 'text-center' },
                { data: 'siaf', className: 'text-center' },//Acciones
                { data: 'occ', className: 'text-center', visible: false },
                { data: 'ruc_entidad', className: 'text-center', visible: false },

            ],
            columnDefs: [
                { className: "text-center", targets: [16] },
                {
                    render: function (data, type, row) {
                        return `<a href="#" title="Ver información adicional" data-orden="${row.nro_orden}" data-id="${row.id}" data-tipo="${row.tipo}" class="info-adicional">${row.nro_orden}</a>`;
                    }, targets: 0 //ID de OC y año
                },
                {
                    render: function (data, type, row) {
                        return (row.descripcion_corta_am==null ? '' : row.descripcion_corta_am) + ( (row.id_acuerdo_marco == '11' || row.id_acuerdo_marco ==null) ? `` : `<button class="btn btn-default btn-xs ver-productos" data-id="${row.id}">Ver prod.</button>`);
                    }, targets: 1
                },
                {
                    render: function (data, type, row) {
                        return `${row.entidad.semaforo} <a title="Ver información de entidad" href="#" class="entidad" data-tipo="${row.tipo}" data-orden="${row.id}" data-id="${row.id_entidad}">${row.nombre_entidad} ${row.id_contacto==null ? '' : '<span class="glyphicon glyphicon-user"></span>'}</a>`;
                    }
                    , targets: 2
                },
                {
                    render: function (data, type, row) {
                        return moment(row.fecha_publicacion).format("DD-MM-YYYY");
                    }
                    , targets: 3
                },
                {
                    render: function (data, type, row) {
                        if (row.estado_oc == 'PUBLICADA' || row.tipo=='directa') {
                            return row.estado_oc;
                        } else {
                            return `<a href="#" class="estados-oc-portal" data-oc="${row.nro_orden}" data-id="${row.id_alternativo}" title="Obtener estados de O/C desde el portal">${row.estado_oc}</a>`;
                        }
                    }, targets: 4 //Estado de O/C
                },
                {
                    render: function (data, type, row) {
                        return row.fecha_estado_format;
                    }, targets: 5
                },
                {
                    render: function (data, type, row) {
                        return (row.inicio_entrega_format=='' ? '(Sin fecha)' : row.inicio_entrega_format)+'/<br>'+row.fecha_entrega_format;
                    }, targets: 6 //Fecha de entrega
                },
                {
                    render: function (data, type, row) {
                        return row.monto_total_format+(row.penalidad_diaria==null ? '' : '<br>(PD: S/ '+$.number(row.penalidad_diaria,2,'.',',')+')');
                    }, targets: 7
                },
                {
                    render: function (data, type, row) {
                        let campos = '';
                        if (permisos.editarOtros == 1) {
                            campos += `<input placeholder="Orden de compra" size="8" data-campo="orden_compra" data-id="${row.id}" data-tipo="${row.tipo}" type="text" class="text-center arriba" value="${row.orden_compra}"><br>
                            <input size="8" placeholder="SIAF" data-campo="siaf" data-id="${row.id}"  data-tipo="${row.tipo}" type="text" class="text-center upper" value="${row.siaf}">`;
                        } else {
                            campos += `<div class="arriba"><strong>OC:</strong> ${row.orden_compra}</div>
                            <div><strong>SIAF:</strong> ${row.siaf}</div>`;
                        }
                        return campos;
                    }, targets: 9 //OC y SIAF
                },
                {
                    render: function (data, type, row) {
                        let campos = '';
                        if (permisos.editarCodGastoFactura == 1) {
                            campos += `<input size="10" placeholder="Factura" data-campo="factura" data-id="${row.id}" data-tipo="${row.tipo}" type="text" class="text-center arriba" value="${row.factura}"><br>
                            <input size="10" placeholder="OCC" data-campo="occ" data-id="${row.id}" data-tipo="${row.tipo}" type="text" class="text-center upper" value="${row.occ}">`;
                        } else {
                            campos += `<div><strong>Fac.:</strong> ${row.factura}</div>
                            <div><strong>OCC.:</strong> ${row.occ}</div>`;
                        }
                        return campos;
                    }, targets: 10 //Factura y OCC
                },
                {
                    render: function (data, type, row) {
                        let campos = '';
                        if (permisos.editarGuiaFecha == 1) {
                            campos += `<input placeholder="Guía" size="10" data-campo="guia" data-id="${row.id}" data-tipo="${row.tipo}" type="text" class="text-center arriba" value="${row.guia}"><br>
                            <input placeholder="Fecha guía" size="10" data-campo="fecha_guia" data-id="${row.id}" data-tipo="${row.tipo}" type="text" class="date-picker text-center" value="${row.fecha_guia_format}">`;
                        } else {
                            campos += `<div><strong>Guía.:</strong> ${row.guia}</div><div><strong>Fecha:</strong> ${row.fecha_guia_format}</div>`;
                        }
                        return campos;
                    }, targets: 11 //Guia y fecha de guia
                },
                {
                    render: function (data, type, row) {
                        $selectEtapas.find('option').removeAttr('selected');
                        $selectEtapas.find('option[value=' + row.id_etapa + ']').attr('selected', 'selected');
                        if (permisos.editarEtapaAdq == 1) {
                            return `<select data-campo="id_etapa" data-tipo="${row.tipo}" data-id="${row.id}">${$selectEtapas.html()}</select>`;
                        } else {
                            return $selectEtapas.find('option:selected').html();
                        }
                    }, targets: 12 //Etapa adquisicion
                },
                {
                    render: function (data, type, row) {
                        $selectCorporativos.find('option').removeAttr('selected');
                        if (row.id_oportunidad == null) {
                            if (permisos.editarOtros == 1) {
                                $selectCorporativos.find('option[value=' + row.id_responsable_oc + ']').attr('selected', 'selected');
                                return `<select class="corporativo" data-campo="id_corporativo" data-tipo="${row.tipo}" data-id="${row.id}">${$selectCorporativos.html()}</select>`;
                            } else {
                                let corporativo = $selectCorporativos.find(`option[value=${row.id_responsable_oc}]`).html();
                                return corporativo == null ? "" : corporativo;
                            }
                        } else {
                            return `${$selectCorporativos.find('option[value=' + row.id_responsable_oc + ']').html()}`;
                        }
                    }, targets: 13 //Responsable
                },
                {
                    render: function (data, type, row) {
                        return row.id_oportunidad == null ? '-' : `${row.codigo_oportunidad}<br>(${row.estado_aprobacion_cuadro} ${row.fecha_aprobacion == '' ? '' : (' - '+row.fecha_aprobacion)})`;
                    }, targets: 14
                },
                {
                    render: function (data, type, row) {
                        let botones = `<div class="btn-group" role="group">
                        <button title="Comentarios" data-orden="${row.nro_orden}" data-tipo="${row.tipo}" data-id="${row.id}" class="btn ${(row.tiene_comentarios == 0 ? 'btn-default' : 'btn-info')} btn-xs ver-comentarios-oc">
                            <span class="glyphicon glyphicon-comment"></span>
                        </button>
                        <button title="Ver información adicional" data-orden="${row.nro_orden}" data-id="${row.id}" data-tipo="${row.tipo}" class="btn btn-default btn-xs info-adicional">
                            <span class="fa fa-info-circle"></span></a>
                        </button><br>
                        <button title="Ver detalles de despacho" class="btn btn-${row.id_despacho!=null ? 'success' : 'default'} btn-xs despacho" data-orden="${row.nro_orden}" data-id="${row.id}" data-tipo="${row.tipo}">
                            <span class="fa fa-truck" aria-hidden="true"></span>
                        </button>`;
                        if (row.id_oportunidad != null) {
                            botones += `<a target="_blank" class="btn btn-warning btn-xs" title="Ver cuadro de presupuesto" href="${rutaCuadroCosto + '/' + row.id_oportunidad}"><span class="glyphicon glyphicon-th-large"></span></a>`;
                        }
                        else {
                            botones += `<button title="Crear cuadro de presupuesto" class="btn btn-default btn-xs crear-cuadro" data-responsable="${row.id_responsable_oc}" data-orden="${row.nro_orden}" data-tipo="${row.tipo}" data-id="${row.id}"><span class="glyphicon glyphicon-th-large"></span></button>`;
                        }
                        botones += '</div>';
                        return botones;
                    }, targets: 15
                }
            ],
            rowCallback: function(row, data) {
                let $class = '';
                if (data.id_responsable_oc == null) {
                    $class = 'flag-blanco';
                } else {
                    if (data.id_oportunidad == null || data.estado_aprobacion_cuadro == 'Inicial') {
                        $class = 'flag-amarillo';
                    } else if (data.estado_aprobacion_cuadro == 'Aprobación pendiente' || data.estado_aprobacion_cuadro == 'Aprobado - pendiente de regularización') {
                        $class = 'flag-naranja';
                    } else if(data.estado_aprobacion_cuadro == 'Aprobado - etapa de compras') {
                        $class = 'flag-azul';
                    } else if(data.estado_aprobacion_cuadro == 'Finalizado') {
                        $class = 'flag-verde';
                    }
                }
                $($(row).find("td")[13]).addClass($class);
                $($(row).find("td")[14]).addClass($class);
            },
            buttons: [
                {
                    text: '<span class="glyphicon glyphicon-filter" aria-hidden="true"></span> Filtros: <span id="spanCantFiltros">0</span>',
                    action: function () {
                        $('#tbodyActualizar').find('td.estado').html('');
                        $('#modalFiltros').modal('show');
                    },
                    className: 'btn-sm'
                },
                {
                    text: '<span class="fa fa-cloud-download" aria-hidden="true"></span> Descargar O/C desde Perú Compras...',
                    action: function () {
                        $('#modalDescargarOrdenes').modal('show');
                    },
                    className: 'btn-sm'
                },
                {
                    text: '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Exportar lista...',
                    action: function () {
                        $('#modalExportarLista').modal('show');
                    },
                    className: 'btn-sm'
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
                    imageColor: "#3c8dbc",
                    zIndex: 10
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });
    }

    verProductosEvent = () => {
        $('#tableOrdenes').on('click', 'button.ver-productos', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $(`<tr class="productos">
                <td></td>
                <td colspan="17">
                    <table class="table table-condensed table-striped productos" style="font-size: small">
                        <thead>
                            <tr>
                                <th class="text-center">Nro.</th>
                                <th class="text-center">Categoría</th>
                                <th class="text-center">Marca</th>
                                <th class="text-center">Modelo</th>
                                <th class="text-center">Nro. parte</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Precio unit.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="7" class="text-center" style="font-size: large"><i class="fa fa-spinner fa-spin"></i></td></tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-center" colspan="7"><button class="btn btn-primary btn-sm ocultar-tabla">Cerrar</button></td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
                <td></td>
            </tr>`).insertAfter($(e.currentTarget).closest('tr'));
            this.model.obtenerProductos($boton.data('id')).then((data) => {
                if (data.tipo == 'success') {
                    const $tbody = $boton.closest('tr').next('tr.productos').find('tbody');
                    let filas = '';
                    for (let indice in data.detalles) {
                        filas += `<tr>
                            <td class="text-center">${parseInt(indice) + 1}</td>
                            <td>${data.detalles[indice].producto.categoria.descripcion}</td>
                            <td class="text-center">${data.detalles[indice].producto.marca}</td>
                            <td class="text-center">${data.detalles[indice].producto.modelo}</td>
                            <td class="text-center">${data.detalles[indice].producto.part_no}</td>
                            <td class="text-center">${data.detalles[indice].cantidad}</td>
                            <td class="text-center">${data.detalles[indice].precio_unitario_format}</td>
                        </tr>`;
                    }
                    $tbody.html(filas);
                } else {
                    Util.notify(data.tipo, data.mensaje);
                    $boton.closest('tr').next('tr.productos').find('button.ocultar-tabla').trigger('click');
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al obtener los productos. Por favor actualice la página e intente de nuevo');
                $boton.closest('tr').next('tr.productos').find('button.ocultar-tabla').trigger('click');
            });
        });

        $('#tableOrdenes').on('click', 'button.ocultar-tabla', (e) => {
            $(e.currentTarget).closest('tr.productos').fadeOut(300, function () {
                //$('#tableOrdenes').find('tbody').find(`tr:eq(${$(this).index()-1})`).find('button.ver-productos').prop('disabled',false);
                $(this).prev('tr').find('button.ver-productos').prop('disabled', false);
                $(this).remove();

            });
        });
    }

    informacionAdicionalEvent = () => {
        $('#tableOrdenes').on("click", ".info-adicional", (e) => {
            e.preventDefault();
            const $modal = $('#modalInformacionAdicional');
            const $elemento = $(e.currentTarget);
            $modal.modal('show');
            $modal.find('div.limpiar').html('');
            $modal.find('span.orden-compra').html($elemento.data('orden'));
            Util.bloquearConSpinner($modal.find('div.mensaje'));
            this.model.obtenerInformacionAdicional($elemento.data('id'),$elemento.data('tipo')).then((data) => {
                if (data.tipo == 'success') {
                    $modal.find('div.lugar-entrega').html(data.lugar_entrega);
                    $modal.find('div.archivos').html(data.archivos);
                    /*$('#btnCambiarContacto').data('entidad', data.orden.id_entidad).data('orden', data.orden.nro_orden).data('id', data.orden.id);
                    $modal.find('div.oc-fisica').html(`<a href="${data.orden.url_oc_fisica}" target="_blank">Descargar</a>`);
                    $modal.find('div.oc-digital').html(`<a href="https://apps1.perucompras.gob.pe//OrdenCompra/obtenerPdfOrdenPublico?ID_OrdenCompra=${data.orden.id}&ImprimirCompleto=1" target="_blank">Descargar</a>`);
                    
                    if (data.orden.contacto != null) {
                        $modal.find('div.nombre').html(data.orden.contacto.nombre);
                        $modal.find('div.telefono').html(data.orden.contacto.telefono);
                        $modal.find('div.cargo').html(data.orden.contacto.cargo);
                        $modal.find('div.correo').html(data.orden.contacto.email);
                        $modal.find('div.direccion').html(data.orden.contacto.direccion);
                        $modal.find('div.horario').html(data.orden.contacto.horario);
                    }*/
                }
                else {
                    alert(data.mensaje);
                    $modal.modal('hide');
                }
            }).fail(() => {
                alert("Hubo un problema al obtener los detalles de la O/C. Por favor actualice la página e intente de nuevo");
                $modal.modal('hide');
            }).always(() => {
                Util.liberarBloqueoSpinner($modal.find('div.mensaje'));
            });
            //this.obtenerInformacionAdicional($(e.currentTarget));
            //$('#modalInformacionAdicional')
        });
    }

    cambiarContactoEvent = () => {

        /*$('#btnCambiarContacto').on('click', (e) => {
            $('#modalCambiarContacto').modal('show');
            contactoView.listarParaSeleccionar($(e.currentTarget));
        });*/

        $('#tableContactosEntidad').on('click', 'button.seleccionar', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            //$boton.html(Util.generarPuntosSvg()+'Seleccionando')
            this.model.cambiarContacto($boton.data('orden'), $boton.data('id'),$boton.data('tipo')).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#tableContactosEntidad').find('td.seleccionado').html('');
                    $boton.closest('tr').find('td.seleccionado').html('<span class="glyphicon glyphicon-ok"></span>');
                    //$('#modalCambiarContacto').modal('hide');
                    //this.obtenerInformacionAdicional($('#btnCambiarContacto'));
                }
            }).fail(() => {
                alert("Hubo un problema al cambiar el contacto. Por favor actualice la página e intente de nuevo");
            }).always(() => {
                $boton.prop('disabled', false);
                //$boton.html('Seleccionar');
            })
        });
    };

    crearCuadroCostoEvent = () => {
        //Actualiza el responsable de la O/C en el botón para crear CC
        $('#tableOrdenes').on("change", "select.corporativo", (e) => {
            $(e.currentTarget).closest('tr').find('button.crear-cuadro').data('responsable', $(e.currentTarget).val());
        });

        $('#tableOrdenes').on("click", "button.crear-cuadro", (e) => {
            const $modal = $('#modalCrearCuadroCosto');
            const $boton = $(e.currentTarget);
            if (this.permisos.crearCuadro == 1 || $boton.data('responsable') == this.idUsuario) {
                $modal.find('span.orden-compra').html($boton.data('orden'));
                $('#txtIdOc').val($boton.data('id'));
                $('#txtTipoOc').val($boton.data('tipo'));
                if ($boton.data('responsable') != 0) {
                    $('#selectOportunidadResponsable').val($boton.data('responsable'));
                }
                $modal.modal('show');
            }
            else {
                alert("No tiene permiso de crear cuadros de presupuesto para órdenes de compra");
            }
        });

        $('#tableOportunidades').on('click', 'button.vincular', (e) => {
            const $elemento = $(e.currentTarget);
            //$elemento.removeClass('vincular');
            $elemento.html('Vinculando');
            $elemento.prop('disabled',true);
            this.model.vincularOportunidad($('#txtIdOc').val(), $('#txtTipoOc').val(), $elemento.data('id')).then((datos) => {
                alert(datos.mensaje);
                if (datos.tipo == 'success') {
                    window.location = this.rutaCuadroCosto + "/" + $elemento.data('id');
                }
            }).fail(() => {
                alert("Hubo un problema al vincular la oportunidad. Por favor actualice la página y vuelva a intentarlo");
            }).always(() => {
                $elemento.prop('disabled',false);
                $elemento.html('Vincular');
            });
        });
    }

    actualizarCamposEvent = () => {
        //let contenedor = "";
        //Actualizar los valores ingresados en la tabla de proformas
        $('#tableOrdenes').on("change", "input[type=text]", (e) => {
            const $elemento = $(e.currentTarget);
            const valor = $elemento.val();
            const $td = $elemento.closest('td');
            $td.removeClass('danger');
            $td.addClass('warning');
            //$elemento.val($elemento.val() + '...');
            this.model.actualizarCampo($elemento.data('id'), $elemento.data('campo'), $elemento.data('tipo'), valor).then((data) => {
                if (data.tipo == 'success') {
                    $elemento.val(valor);
                }
                else {
                    $elemento.val(valor + 'X');
                    $td.addClass('danger');
                    Util.notify(data.tipo, data.mensaje);
                }
            }).fail(() => {
                $elemento.val(valor + 'X');
                $td.addClass('danger');
                Util.notify('error', 'No se pudo guardar la celda marcada en rojo. Elimine la X para volver a intentarlo');
            }).always(() => {
                $td.removeClass('warning');
            });
        });

        $('#tableOrdenes').on("change", "input[type=checkbox], select", (e) => {
            const $elemento = $(e.currentTarget);
            let valor;
            if ($elemento.is('select')) {
                valor = $elemento.val();
            }
            else {
                valor = $elemento.is(':checked');
            }
            const $td = $elemento.closest('td');
            $td.find('div').remove();
            $td.removeClass('danger');
            $td.addClass('warning');
            //$td.append('<span>...</span>');
            this.model.actualizarCampo($elemento.data('id'), $elemento.data('campo'), $elemento.data('tipo'), valor).then((data) => {
                if (data.tipo != 'success') {
                    $td.append('<div><span class="text-danger">(X)</span></div>');
                    Util.notify(data.tipo, data.mensaje);
                }
            }).fail(() => {
                $td.append('<div><span class="text-danger">(X)</span></div>');
                Util.notify('error', 'No se pudo guardar el valor marcado con (X). Por favor vuelva a intentarlo');
            }).always(() => {
                $td.removeClass('warning');
            });
        });
    }

    descargarDesdePortalEvent = () => {

        $('#modalDescargarOrdenes').on('show.bs.modal', () => {
            $('#tbodyDescargarOc').find('tr').each((index, element) => {
                const $fila = $(element);
                const $celda = $fila.find('td.fecha');
                $celda.html('Obteniendo fecha...')
                this.model.obtenerFechaDescargaEmpresa($fila.find('input[type=hidden]:first').data('empresa')).then((data) => {
                    $celda.html(data);
                });
                //console.log($fila.find('input[type=hidden]:first').data('empresa'));
            })
            /*const $tbody = $('#tbodyDescargarOc');
            const $modal=$('#modalDescargarOrdenes');
            const $mensaje=$modal.find('div.mensaje-inicial');
            Util.bloquearConSpinner($mensaje);
            $tbody.html('');
            this.model.obtenerDetallesParaDescargarOc().then((data) => {
                $tbody.html(data);
                Util.liberarBloqueoSpinner($mensaje);
            }).fail(() => {
                alert('Hubo un problema al obtener los datos. Por favor inténtelo de nuevo');
                $modal.modal('hide');
            });*/
        });

        $('#btnDescargarOrdenes').on('click', (e) => {
            const $tbody = $('#tbodyDescargarOc')
            if ($tbody.find('input:checked').length == 0) {
                alert("Seleccione al menos un acuerdo marco");
                return false;
            }
            $tbody.find('td.resultado').html('');
            $tbody.find('input[type=hidden]').removeClass('terminado').addClass('pendiente')
            $('#modalDescargarOrdenes').find('input[type=checkbox]').prop('disabled', true);
            $(e.currentTarget).prop('disabled', true).html(Util.generarPuntosSvg() + ' Descargando');
            //$(e.currentTarget).attr('disabled',true);
            $tbody.find('input[type=checkbox]:checked').each((index, element) => {
                descargarOcDesdePortal($(element).closest('tr'));
            });
        });

        //Seleccionar proformas cotizadas a enviar
        let seleccionarTodo = false;
        $('#chkSeleccionarTodo').on('click', () => {
            $('#tbodyDescargarOc').find('input[type=checkbox]').each(function () {
                $(this).prop('checked', seleccionarTodo);
            });
            seleccionarTodo = !seleccionarTodo;
        });

        const actualizarFechaDescargaEmpresa = ($fila) => {
            const $celda = $fila.find('td.fecha');
            $celda.html('Actualizando fecha...');
            this.model.actualizarFechaDescargaEmpresa($fila.find('input[type=hidden]:first').data('empresa')).then((data) => {
                $celda.html(data);
            });
        }

        const verificarFinProcesoDescarga = () => {
            let totalPendientes = 0;
            $('#tbodyDescargarOc').find('input[type=checkbox]:checked').each((index, element) => {
                totalPendientes += $(element).closest('tr').find('input.pendiente').length;
            });
            if (totalPendientes == 0) {
                $('#modalDescargarOrdenes').find('input, button').prop('disabled', false);
                $('#btnDescargarOrdenes').html('Descargar');
                Util.notify('success', `La descarga de órdenes de compra ha finalizado`);
                $('#tableOrdenes').DataTable().ajax.reload();
            }
        }

        const descargarOcDesdePortal = ($fila) => {
            //Previene la descarga si se oculta el modal
            /*if (!$fila.is(':visible'))
            {
                return false;
            }*/
            progresoDescargaOrdenes($fila);
            const pendientes = $fila.find('input.pendiente').length;
            if (pendientes > 0) {
                const $pendiente = $fila.find('input.pendiente:first');
                this.model.descargarOcDesdePortal($pendiente.data('empresa'), $pendiente.data('catalogo')).then((data) => {
                    if (data.tipo == 'success') {
                        $pendiente.removeClass('pendiente').addClass('terminado');
                        descargarOcDesdePortal($fila);
                    }
                    else {
                        $fila.find('td.resultado').html(`<span class="text-danger">${data.mensaje}</span>`);
                    }

                }).fail(() => {
                    descargarOcDesdePortal($fila);
                });
            }
        }

        const progresoDescargaOrdenes = ($fila) => {
            const terminados = $fila.find('input.terminado').length;
            const total = $fila.find('input[type=hidden]').length;
            let porcentaje = Math.round((terminados * 100) / total);
            Util.generarBarraProgreso($fila.find('td.resultado'), porcentaje);
            if (porcentaje == 100) {
                actualizarFechaDescargaEmpresa($fila);
            }
            verificarFinProcesoDescarga();
        }
    }

}

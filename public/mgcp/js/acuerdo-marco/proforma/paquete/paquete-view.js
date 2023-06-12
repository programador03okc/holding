class PaqueteView {
    constructor(model, tipoProforma, idUsuario) {
        this.model = model;
        this.tipoProforma = tipoProforma; //0 ordinaria, 1 gran compra
        this.idUsuario = idUsuario;
    }

    obtenerProformas(reiniciarPaginacion = false) {
        $('#btnBuscar').prop('disabled', true);
        $('#txtCriterioHidden').val($('#txtCriterio').val());
        const $contenedorProformas = $('#divContenedorProformas');
        const $bodyProformas = $('#divBodyProformas');
        const $footerProformas = $('#divFooterProformas');
        //$bodyProformas.html('<div class="text-center">Obteniendo datos...</div>');
        $contenedorProformas.LoadingOverlay("show", {
            imageAutoResize: true,
            progress: true,
            imageColor: "#3c8dbc",
            zIndex: 10
        });
        if (reiniciarPaginacion) {
            $('#txtNroPaginaHidden').val(1);
        }
        this.actualizarCantidadFiltrosAplicados();
        this.model.obtenerProformas($('#formFiltros').serialize()).then((respuesta) => {
            $bodyProformas.html(respuesta.body);
            $footerProformas.html(respuesta.footer);
            $('body').find('input[type=checkbox]:checked').each((index, element) => {
                this.actualizarDescripcionProductoSeleccionado($(element))
            })
        }).always(() => {
            $contenedorProformas.LoadingOverlay("hide", true);
            $('#btnBuscar').prop('disabled', false);
        }).fail(() => {
            $bodyProformas.html('<div class="text-center">Hubo un problema al obtener las proformas. Por favor actualice la página e intente de nuevo</div>')
            $footerProformas.html('');
        });
    }

    actualizarCantidadFiltrosAplicados() {
        $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
    }

    actualizarCostoEnvioEvent() {
        let contenedor = "";
        $('body').on("focus", "td.flete", function () {
            contenedor = $(this).html();
        });

        $('body').on("blur", "td.flete", (e) => {
            const $celda = $(e.currentTarget);
            if (contenedor !== $celda.html()) {
                let valor = "";
                if ($celda.html() != "") {
                    valor = $.number($celda.html().split(',').join(''), 2, '.', ',');
                }
                $celda.removeClass('success').removeClass('danger').addClass('warning');
                //console.log($celda.data('requerimiento-entrega'));
                this.model.actualizarCostoEnvio($celda.data('requerimiento-entrega'), $celda.data('proforma'), $celda.data('empresa'), valor).then((data) => {
                    let clase = '';
                    switch (data.tipo) {
                        case 'error':
                            clase = 'danger';
                            $celda.html(valor);
                            Util.notify(data.tipo, data.mensaje);
                            break;
                        case 'warning':
                            clase = 'success'; //Para que se pueda seguir editando la celda
                            $celda.html(contenedor);
                            Util.notify(data.tipo, data.mensaje);
                            break;
                        default:
                            clase = 'success';
                            $celda.html(valor);
                            break;
                    }
                    $celda.addClass(clase);

                }).fail(() => {
                    $celda.addClass('danger');
                    $celda.html(valor + 'X');
                    Util.notify('error', 'No se pudo guardar la celda marcada en rojo. Elimine la X para volver a intentarlo');
                }).always(() => {
                    $celda.removeClass('warning');
                });
            }
        });
    }

    actualizarFiltrosEvent() {
        let actualizar = false;
        $('#modalFiltros').find('input[type=checkbox]').change(function () {
            actualizar = true;
        });

        $('#modalFiltros').find('input[type=text], select').change((e) => {
            if ($(e.currentTarget).hasClass('actualizar') || $(e.currentTarget).closest('div.form-group').find('input[type=checkbox]').is(':checked') == true) {
                actualizar = true;
            }
        });

        $("#modalFiltros").on("hidden.bs.modal", () => {
            if (actualizar) {
                actualizar = false;
                this.obtenerProformas(true);
            }
        });
    }

    paginarResultadoEvent() {
        $('#divFooterProformas').on('click', 'button.anterior', (e) => {
            const $pagina = $('#divFooterProformas').find('select.pagina');
            if ($pagina.val() > 1) {
                $pagina.val(parseInt($pagina.val()) - 1);
                $('#txtNroPaginaHidden').val($pagina.val());
                this.obtenerProformas();
            }
        });

        $('#divFooterProformas').on('click', 'button.siguiente', (e) => {
            const $pagina = $('#divFooterProformas').find('select.pagina');
            if ($pagina.val() != 0 && $pagina.val() < $pagina.find('option').length) {
                $pagina.val(parseInt($pagina.val()) + 1);
                $('#txtNroPaginaHidden').val($pagina.val());
                this.obtenerProformas();
            }
        });

        $('#divFooterProformas').on('change', 'select.pagina', (e) => {
            $('#txtNroPaginaHidden').val($(e.currentTarget).val());
            this.obtenerProformas();
        });
    }

    mostrarDetallesProformaEvent() {
        $('#divBodyProformas').on('click', 'button.mostrar', (e) => {
            const $boton = $(e.currentTarget);
            $boton.removeClass('mostrar').addClass('ocultar').html('<span class="glyphicon glyphicon-minus"></span>')
            $boton.closest('div.panel').find('div.panel-body').fadeIn(300);
        });

        $('#divBodyProformas').on('click', 'button.ocultar', (e) => {
            const $boton = $(e.currentTarget);
            $boton.removeClass('ocultar').addClass('mostrar').html('<span class="glyphicon glyphicon-plus"></span>')
            $boton.closest('div.panel').find('div.panel-body').fadeOut(300);
        });
    }

    realizarBusquedaEvent() {
        $('#btnBuscar').on('click', (e) => {
            this.obtenerProformas(true);
        })

        $('#txtCriterio').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnBuscar').trigger('click');
            }
        });
    }

    actualizarDescripcionProductoSeleccionado($check) {
        let descripcionProducto = $check.closest('table').find('a.producto[data-id="' + $check.data('producto') + '"]:eq(0)').html()
        $check.closest('div.panel-requerimiento').find('table.envio').each(function () {
            $(this).find('td.producto').each(function () {
                if ($(this).hasClass($check.attr('class'))) {
                    $(this).html($check.is(':checked') ? descripcionProducto : '');
                }
            })
        })
    }

    actualizarSeleccionEvent() {
        $('#divContenedorProformas').on('change', 'input[type=checkbox]', (e) => {
            //const $tbody=;
            //console.log($(e.currentTarget).attr("class"));
            const $check = $(e.currentTarget);
            $check.closest('td').removeClass('danger').addClass('warning');
            const estado = $check.prop('checked');
            $check.closest('tbody').find(`input[type=checkbox].${$check.attr("class")}`).prop('checked', false);
            $check.prop('checked', estado);
            this.actualizarDescripcionProductoSeleccionado($check);
            this.model.actualizarSeleccion($check.data('id'), estado).then((data) => {
                if (data.tipo != 'success') {
                    Util.notify(data.tipo, data.mensaje);
                    $check.closest('td').addClass(data.tipo == 'error' ? 'danger' : data.tipo);
                }

            }).fail(() => {
                $check.closest('td').addClass('danger');
                Util.notify('error', "Hubo un problema al actualizar la selección del producto. Por favor intente de nuevo");
            }).always(() => {
                $check.closest('td').removeClass('warning');
            });
        });
    }

    actualizarPrecioPublicarEvent() {
        let contenedor = "";
        $('body').on("focus", "td.precio", function () {
            contenedor = $(this).html();
        });

        $('body').on("blur", "td.precio", (e) => {
            const $celda = $(e.currentTarget);
            if (contenedor !== $celda.html()) {
                let valor = "";
                if ($celda.html() != "") {
                    valor = $.number($celda.html().split(',').join(''), 2, '.', ',');
                }
                $celda.removeClass('success').removeClass('danger').addClass('warning');
                this.model.actualizarPrecio($celda.data('id'), valor).then((data) => {
                    if (data.tipo != 'success') {
                        Util.notify(data.tipo, data.mensaje);
                    }
                    console.log(data.tipo + ': ' + data.mensaje)
                    $celda.addClass(data.tipo == 'error' ? 'danger' : data.tipo);
                    $celda.html(valor);
                }).fail(() => {
                    $celda.addClass('danger');
                    $celda.html(valor + 'X');
                    Util.notify('error', 'No se pudo guardar la celda marcada en rojo. Elimine la X para volver a intentarlo');
                }).always(() => {
                    $celda.removeClass('warning')
                });
            }
        });
    }

    enviarCotizacionesEvent() {
        //Enviar lista al portal
        $('#btnEnviarProformas').on('click', (e) => {
            const $tbody = $('#tbodyProformasEnviar');
            if ($tbody.find('input:checked').length == 0) {
                alert("Seleccione al menos una proforma a enviar");
                return false;
            }
            $(e.currentTarget).html(Util.generarPuntosSvg() + ' Enviando');

            let empresas = [];
            $('#modalProformasEnviar').find('button,input').prop('disabled', true);
            $tbody.find('td.resultado').html('');
            $tbody.find('input:checked').each((index, element) => {
                const $fila = $(element).closest('tr');
                $fila.find('td.resultado').html('En espera');
                empresas.push($fila.find('td.empresa').html());
            });
            progresoEnvioProformas();
            let listaEmpresas = Array.from(new Set(empresas));
            listaEmpresas.forEach((element, index) => {
                enviarProforma(element);
            });
        });

        $('#modalProformasEnviar').on('show.bs.modal', (e) => {
            $('#tbodyProformasEnviar').html('');
            $('#divProformasEnviarMensaje').html('');
        });

        $('#modalProformasEnviar').on('shown.bs.modal', (e) => {
            const $modal = $('#modalProformasEnviar');
            //const $mensajeInicial = $modal.find('div.mensaje-inicial');
            $('#chkSeleccionarTodo').prop('checked', false);
            //$('#divProformasEnviarMensaje').html('');
            $modal.find('input').val('');
            $('#btnEnviarProformas').html('Enviar');
            $modal.find('div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc"
            });
            //$('#divProformasEnviarMensaje').html('');
            //Util.bloquearConSpinner($mensajeInicial);

            this.model.obtenerListaParaEnviarPortal().then((datos) => {
                let filas = '';
                let requerimientoEmpresa = '';
                let imprimirCeldaConRowSpan = false;
                let contadorFilas = 0;
                let contadorPintar = 0;
                let pintar = true;
                
                for (let indice in datos) {
                    if (datos[indice].total_proformas == datos[indice].total_proformas_seleccionadas) {

                        if (requerimientoEmpresa != datos[indice].requerimiento + datos[indice].empresa) {
                            requerimientoEmpresa = datos[indice].requerimiento + datos[indice].empresa;
                            imprimirCeldaConRowSpan = true;
                        }

                        filas += `<tr${pintar ? ' class="fondo-plomo"' : ''}>`;
                        if (imprimirCeldaConRowSpan) {
                            filas += `<td rowspan="${datos[indice].total_proformas}" class="text-center">${parseInt(contadorFilas) + 1}</td>
                                    <td rowspan="${datos[indice].total_proformas}" class="text-center">${datos[indice].requerimiento}</td>
                                    <td rowspan="${datos[indice].total_proformas}" class="text-center fecha">${datos[indice].fecha_limite}</td>
                                    <td rowspan="${datos[indice].total_proformas}" class="text-center empresa">${datos[indice].empresa}</td>
                                    <td rowspan="${datos[indice].total_proformas}">${datos[indice].lugar_entrega}</td>`;
                            contadorFilas++;
                        }
                        filas += `<td class="text-center">${datos[indice].proforma}</td>
                        <td class="text-center">${datos[indice].marca} ${datos[indice].modelo}</td>
                        <td class="text-center">${datos[indice].part_no}</td>`;
                        if (imprimirCeldaConRowSpan) {
                            filas += `<td rowspan="${datos[indice].total_proformas}" class="usuario">`;
                            if (datos[indice].nombre_corto != null) {
                                filas += datos[indice].nombre_corto;
                            }
                            filas += `</td>`;
                        }
                        filas += `<td class="text-center">${(datos[indice].moneda_ofertada == 'USD' ? '$' : 'S/')}${$.number(datos[indice].precio_publicar, 2, '.', ',')}</td>
                        <td class="text-center">
                        ${datos[indice].requiere_flete == '0' ? 'N/R' : 'S/' + $.number(datos[indice].costo_envio_publicar, 2, '.', ',')}
                        ${datos[indice].restringir ? '<div><strong class="text-danger">RESTRINGIR</strong></div>' : ''}
                        </td>`;
                        if (imprimirCeldaConRowSpan) {
                            filas += `<td rowspan = "${datos[indice].total_proformas}" class="text-center"> <input type="checkbox" data-requerimiento="${datos[indice].id}"`;

                            if (datos[indice].id_ultimo_usuario == this.idUsuario) {
                                filas += 'checked';
                            }
                            filas += `></td>
                            <td rowspan="${datos[indice].total_proformas}" class="text-center resultado"></td>`;
                        }
                        filas += `</tr> `;
                        imprimirCeldaConRowSpan = false;
                        contadorPintar++;
                        if (contadorPintar == datos[indice].total_proformas) {
                            contadorPintar = 0;
                            pintar = !pintar;
                        }
                    }
                }
                $('#tbodyProformasEnviar').html(filas);
                $modal.find('button,input').attr('disabled', false);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert('Hubo un problema al obtener los datos. Por favor actualice la página y vuelva a intentarlo');
                $modal.modal('hide');
            }).always(() => {
                $modal.find('div.modal-body').LoadingOverlay("hide", true);
            });
        });

        //Seleccionar proformas cotizadas a enviar
        let seleccionarTodo = true;
        $('#chkSeleccionarTodo').on('click', (e) => {
            $('#tbodyProformasEnviar').find('input[type=checkbox]:visible').each(function () {
                $(this).prop('checked', seleccionarTodo);
            });
            seleccionarTodo = !seleccionarTodo;
        });


        //Buscar en lista de proformas a enviar
        $('#txtBuscarEmpresa, #txtBuscarUsuario, #txtBuscarFechaLimite').on('keyup', (e) => {
            let criterioEmpresa = $('#txtBuscarEmpresa').val().toLowerCase();
            let criterioUsuario = $('#txtBuscarUsuario').val().toLowerCase();
            let criterioFecha = $('#txtBuscarFechaLimite').val();

            $('#tbodyProformasEnviar').find('tr').each(function () {
                if ($(this).find('td.empresa').html().toLowerCase().includes(criterioEmpresa) == false || $(this).find('td.usuario').html().toLowerCase().includes(criterioUsuario) == false || $(this).find('td.fecha').html().includes(criterioFecha) == false) {
                    $(this).hide();
                }
                else {
                    $(this).show();
                }
            });
        });

        const enviarProforma = (empresa) => {
            let $fila;
            $('#tbodyProformasEnviar').find('tr').each(function () {
                if ($(this).find('td.empresa').html() == empresa && $(this).find('td.resultado').html() == 'En espera') {
                    $fila = $(this);
                    return false;
                }
            });

            if ($fila != null) {
                $fila.find('td.resultado').html('Enviando...');
                this.model.enviarCotizacionPortal($fila.find('input[type=checkbox]').data('requerimiento')).then((data) => {
                    $fila.find('td.resultado').html(`<span class="text-${data.tipo}"> ${data.mensaje}</span>`);
                    progresoEnvioProformas();
                    enviarProforma(empresa);
                }).fail(() => {
                    $fila.find('td.resultado').html(`En espera`);//Para que vuelva a intentar
                    enviarProforma(empresa);
                });
            }
        }

        const progresoEnvioProformas = () => {
            const $seleccionados = $('#tbodyProformasEnviar').find('input[type=checkbox]:checked');
            let totalProformas = $seleccionados.length;
            let proformasEnviadas = 0;
            $seleccionados.each((index, element) => {
                //const $fila = $(element).closest('tr');
                let resultado = $(element).closest('tr').find('td.resultado').html();
                if (!resultado.includes('En espera') && !resultado.includes('Enviando')) {
                    proformasEnviadas += 1;
                }
            });
            //$('#tbodyProformasEnviar').find('td.resultado:not(:empty)').find(':not(:contains(En espera))').length;
            let porcentaje = Math.round((proformasEnviadas * 100) / totalProformas);
            Util.generarBarraProgreso('#divProformasEnviarMensaje', porcentaje);
            if (porcentaje >= 100) {
                $('#btnEnviarProformas').html('Enviar');
                $('#modalProformasEnviar').find('button,input').prop('disabled', false);
                //const seleccionados = $('#tbodyProformasEnviar').find('td.resultado:contains(Enviada)').length;
                Util.notify('success', `El proceso ha finalizado.Se ${(totalProformas > 1 ? `han enviado ${totalProformas} proformas` : 'ha enviado 1 proforma')} al portal`);
                this.obtenerProformas(true);//$('#tableProformas').DataTable().ajax.reload();
            }
        }
    }


    /*obtenerCantidadPendientes=()=>{
        this.model.obtenerCantidadPendientes().then((data) => {
            $('#spanCantidadProformaPaquete').html(data.cantidad);
        }).fail(() => {
            
        });
    }*/
}
import ProformaView from "../proforma-view.js";

export default class ProformaIndividualView extends ProformaView {

    constructor(model, idUsuario) {
        super(model, idUsuario);
    }

    actualizarCamposEvent() {
        let contenedor = "";
        $('body').on("focus", "td.success:not(.restringir), td.danger:not(.restringir)", function () {
            contenedor = $(this).html();
        });

        $('body').on("blur", "td.success:not(.restringir), td.danger:not(.restringir)", (e) => {
            const $celda = $(e.currentTarget);
            if (contenedor !== $celda.html()) {
                let valor = "";
                if ($celda.html() != "") {
                    valor = Util.formatoNumero($celda.html().split(',').join(''), $celda.hasClass('decimal') ? 2 : 0);
                }
                $celda.removeClass('success').removeClass('danger').addClass('warning');
                this.model.actualizarCampo($celda.data('id'), $celda.data('campo'), valor).then((data) => {
                    $celda.removeClass('warning').addClass('success');
                    $celda.html(valor);
                }).fail(() => {
                    $celda.removeClass('warning').addClass('danger');
                    $celda.html(valor + 'X');
                    Util.notify('error', 'No se pudo guardar la celda marcada en rojo. Elimine la X para volver a intentarlo');
                });
            }
        });
    }

    actualizarRestringirEvent() {
        $('body').on("change", "input[name=restringir]", (e) => {
            const $check = $(e.currentTarget);
            const $celda = $check.closest('td');
            const $fila = $celda.closest('tr');
            $celda.addClass('warning');
            this.model.actualizarRestringir($check.data('id'), ($check.is(':checked') ? 1 : 0)).then((data) => {
                if (data.tipo == 'success') {
                    if ($check.is(':checked')) {
                        $fila.find('td.precio').removeClass('success').prop('contenteditable', false);
                        $fila.find('td.flete').removeClass('success').prop('contenteditable', false);
                        $fila.find('td.plazo').removeClass('success').prop('contenteditable', false);
                    } else {
                        $fila.find('td.precio').addClass('success').prop('contenteditable', true);
                        $fila.find('td.plazo').addClass('success').prop('contenteditable', true);
                        if ($fila.find('td.flete').html() != 'N/R') {
                            $fila.find('td.flete').addClass('success').prop('contenteditable', true);
                        }

                    }
                }
                else {
                    $celda.addClass('danger');
                }
                Util.notify(data.tipo, data.mensaje);
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al actualizar la restricción. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $celda.removeClass('warning');
            });
        });
    }

    deshacerCotizacionEvent() {
        //Deshacer cotizacion
        let $fila;
        $('body').on('click', 'a.deshacer', (e) => {
            e.preventDefault();
            const $elemento = $(e.currentTarget);
            $fila = $elemento.closest('td').closest('tr');
            const $modal = $('#modalDeshacerCotizacion')
            const $mensaje = $modal.find('div.mensaje');
            const $botonDeshacer = $('#btnDeshacerCotizacion');
            $modal.find('div.modal-body').find('span').html('');
            $botonDeshacer.data('id', $elemento.data('id'));

            Util.bloquearConSpinner($mensaje);
            $botonDeshacer.prop('disabled', true);
            $modal.modal('show');
            this.model.obtenerDetalles($elemento.data('id')).then((data) => {
                $modal.find('span.proforma').html(data.proforma);
                $modal.find('span.requerimiento').html(data.requerimiento);
                $modal.find('span.producto').html(`${data.producto.marca} ${data.producto.modelo} ${data.producto.part_no}`);
                $modal.find('span.entidad').html(data.entidad.nombre);
                $modal.find('span.empresa').html(data.empresa.empresa);
                $modal.find('span.precio-publicar').html(`${(data.moneda_ofertada == 'USD' ? '$' : 'S/')}${Util.formatoNumero(data.precio_publicar, 2)}`);
                $modal.find('span.flete-publicar').html(`S/${Util.formatoNumero(data.costo_envio_publicar, 2)}`);
                //$botonDeshacer.data('precio', data.precio_publicar);
                //$botonDeshacer.data('flete', data.costo_envio_publicar);
                $botonDeshacer.prop('disabled', false);
                Util.liberarBloqueoSpinner($mensaje);
            }).fail(() => {
                alert('Hubo un problema al obtener los detalles de la proforma. Por favor actualice la página e intente de nuevo');
                $modal.modal('hide');
            });
        });

        //Deshacer cotización
        $('#btnDeshacerCotizacion').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(`${Util.generarPuntosSvg()} Procesando`);

            this.model.deshacerCotizacion($boton.data('id')).then((data) => {

                if (data.tipo == 'success') {
                    Util.notify(data.tipo, data.mensaje);
                    $fila.find('td.estado').html('PENDIENTE');
                    $fila.find('td.plazo').addClass('success').prop('contenteditable', true).html(data.plazo);
                    $fila.find('td.precio').addClass('success').prop('contenteditable', true).html(Util.formatoNumero(data.precio, 2));
                    if (data.requiereFlete == '1') {
                        $fila.find('td.flete').addClass('success').prop('contenteditable', true).html(Util.formatoNumero(data.flete, 2));
                    }
                    $('#modalDeshacerCotizacion').modal('hide');
                }
                else {
                    alert(data.mensaje);
                }
            }).fail(function () {
                alert('Hubo un problema al intentar deshacer la cotización. Por favor actualice la página e intente de nuevo');
            }).always(function () {
                $boton.prop('disabled', false);
                $boton.html('Deshacer cotización');
            });
        });
    }

    enviarCotizacionesEvent() {
        //Enviar lista al portal
        $('#btnEnviarProformas').on('click', (e) => {
            const $tbody=$('#tbodyProformasEnviar');
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
            const $modal = $('#modalProformasEnviar');
            const $contenedor = $('#tbodyProformasEnviar');
            const $mensajeInicial = $modal.find('div.mensaje-inicial');
            $('#chkSeleccionarTodo').prop('checked', false);
            $('#divProformasEnviarMensaje').html('');
            $modal.find('input').val('');
            $('#btnEnviarProformas').html('Enviar');
            $('#divProformasEnviarMensaje').html('');
            Util.bloquearConSpinner($mensajeInicial);
            $contenedor.html('');
            this.model.obtenerListaParaEnviarPortal().then((datos) => {
                let filas = '';
                for (let indice in datos) {
                    filas += `<tr data-id="${datos[indice].nro_proforma}">
                    <td class="text-center">${parseInt(indice) + 1}</td>
                    <td class="text-center">${datos[indice].proforma}</td>
                    <td class="text-center">${datos[indice].producto.marca} ${datos[indice].producto.modelo}</td>
                    <td class="text-center">${datos[indice].producto.part_no}</td>
                    <td class="text-center empresa">${datos[indice].empresa.empresa}</td>
                    <td>${datos[indice].lugar_entrega}</td>
                    <td class="text-center fecha">${datos[indice].fecha_limite}</td>
                    <td class="usuario">`;
                    if (datos[indice].usuario != null) {
                        filas += datos[indice].usuario.nombre_corto;
                    }
                    filas += `</td>
                    <td class="text-center">${(datos[indice].moneda_ofertada == 'USD' ? '$' : 'S/')}${Util.formatoNumero(datos[indice].precio_publicar, 2)}</td>
                    <td class="text-center">
                    ${datos[indice].requiere_flete == '0' ? 'N/R' : 'S/' + Util.formatoNumero(datos[indice].costo_envio_publicar, 2)}
                    ${datos[indice].restringir ? '<div><strong class="text-danger">RESTRINGIR</strong></div>' : ''}
                    </td>
                    <td class="text-center"><input type="checkbox" data-empresa="${datos[indice].empresa.empresa}"`;
                    if (datos[indice].usuario != null && datos[indice].usuario.id == this.idUsuario) {
                        filas += 'checked';
                    }
                    filas += `></td>
                    <td class="text-center resultado"></td>
                    </tr>`;
                }
                $contenedor.html(filas);
                $modal.find('button,input').attr('disabled', false);
                Util.liberarBloqueoSpinner($mensajeInicial);
            }).fail(function () {
                alert('Hubo un problema al obtener los datos. Por favor actualice la página y vuelva a intentarlo');
                $modal.modal('hide');
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
                this.model.enviarCotizacionPortal($fila.data('id')).then((data) => {
                    $fila.find('td.resultado').html(`<span class="text-${data.tipo}">${data.mensaje}</span>`);
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
                const $fila = $(element).closest('tr');
                if ($(element).closest('tr').find('td.resultado').html() != 'En espera') {
                    proformasEnviadas += 1;
                }
            });
            $('#tbodyProformasEnviar').find('td.resultado:not(:empty)').find(':not(:contains(En espera))').length;
            let porcentaje = Math.round((proformasEnviadas * 100) / totalProformas);
            Util.generarBarraProgreso('#divProformasEnviarMensaje', porcentaje);
            if (porcentaje >= 100) {
                $('#btnEnviarProformas').html('Enviar');
                $('#modalProformasEnviar').find('button,input').prop('disabled', false);
                //const seleccionados = $('#tbodyProformasEnviar').find('td.resultado:contains(Enviada)').length;
                Util.notify('success', `El proceso ha finalizado. Se ${(totalProformas > 1 ? `han enviado ${totalProformas} proformas` : 'ha enviado 1 proforma')}  al portal`);
                $('#tableProformas').DataTable().ajax.reload();
            }
        }
    }

    probabilidadGanar() {
        $('body').on('change', '.check-mpg', (e) => {
            const $celda = $(e.currentTarget);
            var producto = $celda.data('producto');
            var proforma = $celda.data('proforma');
            var nroproforma = $celda.data('nro');
            var claseActiva = 'check-activo-' + proforma + '-' + producto;
            var valor = false;

            if ($celda.is(':checked') ) {
                $('.mpg-' + proforma + '-' + producto).addClass(claseActiva);
                $('.mpg-' + proforma + '-' + producto).prop('checked', false);
                $celda.prop('checked', true);
                $('.analisis-' + proforma + '-' + producto).data('nro', nroproforma);
                $celda.addClass(claseActiva);
                valor = true;
            } else {
                $('.mpg-' + proforma + '-' + producto).removeClass(claseActiva);
            }

            this.model.actualizarProbabilidad(nroproforma, proforma, producto, valor).then((data) => {
                if (data.response == 'ok') {
                    Util.notify(data.tipo, data.mensaje);
                } else {
                    Util.notify(data.tipo, data.mensaje);
                }
            }).fail(() => {
                Util.notify('error', 'No se pudo seleccionar la MPG, volver a intentarlo');
            });
        });
    }
}
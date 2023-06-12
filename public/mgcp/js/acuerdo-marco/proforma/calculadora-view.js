class CalculadoraView {
    constructor(model) {
        this.model = model;
        this.$botonCalculadora = null;
    }

    listarEvent() {
        const $modal = $('#modalCalculadora');

        $('#divContenedorProformas').on('click', 'a.calculadora', (e) => {
            e.preventDefault();
            this.$botonCalculadora = $(e.currentTarget);
            $modal.modal('show');
            $modal.find('span.producto').empty();
            $modal.find('span.cantidad').empty();
            $modal.find('span.proforma').html(this.$botonCalculadora.data('proforma'));
            $('#tbodyCalculadoraProducto').empty();
            $('#tbodyCalculadoraEmpresas').empty();
            $('#btnAplicarPreciosCalculadora').prop('disabled', true);
        })

        $modal.on('shown.bs.modal', () => {
            $modal.find('div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc",
                zIndex: 2000
            });
            const $modalFiltros = $('#modalFiltros');
            let empresas = $modalFiltros.find('input[name=chkEmpresa]').is(':checked') ? $modalFiltros.find('select.empresa').val() : null;
            let estadoProformas = $modalFiltros.find('input[name=chkEstado]').is(':checked') ? $modalFiltros.find('select[name=selectEstado]').val() : null;
            let nroProforma = this.$botonCalculadora.data('nro');
            this.model.listar(this.$botonCalculadora.data('tipo'), this.$botonCalculadora.data('requerimiento'), this.$botonCalculadora.data('proforma'), this.$botonCalculadora.data('producto'), empresas, estadoProformas, nroProforma).then((data) => {
                const monedaProforma = data.proformas[0].moneda_ofertada;
                const $botonAgregarFila = $('#btnCalculadoraAgregarFila');

                $botonAgregarFila.data('moneda', monedaProforma);
                $botonAgregarFila.data('proforma', nroProforma);
                $botonAgregarFila.data('producto', this.$botonCalculadora.data('producto'));
                $botonAgregarFila.data('tipo', this.$botonCalculadora.data('tipo'));

                $modal.find('span.producto').html(`${data.producto.descripcion} (<a target="_blank" href="${data.producto.ficha_tecnica}">Ficha</a>)`);
                $modal.find('span.cantidad').html(this.$botonCalculadora.data('cantidad'));
                this.mostrarFilasTablaCostos(data.calculadora, data.detalles, monedaProforma, nroProforma, this.$botonCalculadora.data('tipo'));
                this.mostrarFilasTablaEmpresas(this.$botonCalculadora.data('tipo'), data.proformas);
                this.calcularCostoTotal();
                this.calcularMargenGanacia();
                $('#btnAplicarPreciosCalculadora').prop('disabled', false);
            }).always(() => {
                $modal.find('div.modal-body').LoadingOverlay("hide", true);
            }).fail(() => {
                alert("Hubo un problema al mostrar la calculadora. Por favor actualice la página e intente de nuevo");
                $modal.modal('hide');
            });
        })
    }

    agregarFilaEvent() {
        $('#btnCalculadoraAgregarFila').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            this.model.agregarFila($boton.data('producto'), $boton.data('proforma'), $boton.data('tipo')).then((data) => {
                if (data.tipo == 'success') {
                    $('#tbodyCalculadoraProducto').append(`<tr>
                    <td colspan="2">
                        <input type="text" data-id="${data.id}" data-campo="concepto" data-proforma="${data.proforma}" class="form-control input-sm" placeholder="Concepto" value="">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">${$boton.data('moneda')}</span>
                            <input type="text" data-id="${data.id}" data-campo="monto" data-proforma="${data.proforma}" class="form-control text-right input-sm costo decimal" placeholder="Costo" value="0">
                        </div>
                    </td>
                    <td class="text-center">
                        <button data-id="${data.id}" class="btn btn-xs btn-danger eliminar"><span class="glyphicon glyphicon-remove"></span></button>
                    </td>
                </tr>`);
                }
                else {
                    Util.notify(data.tipo, data.mensaje);
                }
            }).always(() => {
                $boton.prop('disabled', false);
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al agregar la fila. Por favor actualice la página e intente de nuevo');
            })
        });
    }

    calcularCostoTotalEvent() {
        $('#tbodyCalculadoraProducto').on('keyup', 'input.costo, input.flete', () => {
            this.calcularCostoTotal();
        })
    }

    calcularMargenGanancialEvent() {
        $('#tbodyCalculadoraEmpresas').on('keyup', 'input.margen', () => {
            this.calcularMargenGanacia();
        })
    }

    calcularCostoTotal() {
        const $botonFila = $('#btnCalculadoraAgregarFila');
        let costo = parseFloat($('#tbodyCalculadoraProducto').find('input.flete').val());
        $('#tbodyCalculadoraProducto').find('input.costo').each((index, element) => {
            costo += ($(element).val() == '' ? 0 : parseFloat($(element).val()));
        })
        //$botonFila.data('costo', costo);
        $('#tdCalculadoraCostoTotal').html(`<strong>${$botonFila.data('moneda')} ${Util.formatoNumero(costo, 2)}</strong>`);
        this.calcularMargenGanacia();
    }

    calcularMargenGanacia() {
        let moneda = $('#btnCalculadoraAgregarFila').data('moneda');
        //let tipoCambio = 1;
        let costoTotal = 0;
        $('#tbodyCalculadoraProducto').find('input.costo').each((index, element) => {
            costoTotal += ($(element).val() == '' ? 0 : parseFloat($(element).val()));
        })
        let flete=parseFloat($('#tbodyCalculadoraProducto').find('input.flete').val());
        $('#tbodyCalculadoraEmpresas').find('tr').each((index, element) => {
            //if ($(element).find('input[type=checkbox]').is(':checked')) {
                let margen = parseFloat($(element).find('input.margen').val());
                let subtotal =(costoTotal / (1 - (margen / 100)))+flete;
                let precioBase = parseFloat($(element).find('td.precio-base').data('base'));
                let tipoCambio= parseFloat($(element).find('td.precio-base').data('tc'));
                if (subtotal > precioBase) {
                    $(element).find('input.precio').val(Util.formatoNumero(precioBase, 2, '.', ''));
                    let flete = (subtotal - precioBase) * (moneda == 'USD' ? tipoCambio : 1);
                    let precioBaseSoles = precioBase * (moneda == 'USD' ? tipoCambio : 1);
                    let fleteMaximo = precioBaseSoles;// * 0.9999;
                    $(element).find('input.flete').val(Util.formatoNumero((flete > fleteMaximo ? fleteMaximo : flete), 2, '.', ''));
                    if (flete > fleteMaximo) {
                        $(element).find('td.excedente').html(`<span class="text-danger"><strong>${Util.formatoNumero((flete - fleteMaximo), 2)}</strong></span>`);
                    }
                    else {
                        $(element).find('td.excedente').html('0.00');
                    }
                }
                else {
                    $(element).find('input.precio').val(Util.formatoNumero(subtotal, 2, '.', ''));
                    $(element).find('input.flete').val('0.00');
                    $(element).find('td.excedente').html('0.00');
                }
            //}
        });
    }

    aplicarPreciosEnVistaPrincipal() {
        const $tablaVistaPrincipal = this.$botonCalculadora.closest('tr').find('tbody');
        const $tablaCalculadora = $('#tbodyCalculadoraEmpresas');
        const cantFilas = $tablaCalculadora.find('tr').length;
        for (let i = 0; i < cantFilas; i++) {
            let $filaCalculadora = $tablaCalculadora.find(`tr:eq(${i})`);
            if ($filaCalculadora.find('td.estado').html() == 'PENDIENTE' && $filaCalculadora.find('input[type=checkbox]').is(':checked')) {
                let $filaProforma = $tablaVistaPrincipal.find(`tr:eq(${i})`);
                $filaProforma.find('td.precio').html(Util.formatoNumero($filaCalculadora.find('input.precio').val(), 2));
                $filaProforma.find('td.flete').html(Util.formatoNumero($filaCalculadora.find('input.flete').val(), 2));
            }
        }
    }

    aplicarPreciosProformaEvent() {
        $('#btnAplicarPreciosCalculadora').on('click', (e) => {
            $(e.currentTarget).prop('disabled', true);
            this.model.aplicarPreciosProformas($('#formCalculadoraPreciosPublicar').serialize()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#modalCalculadora').modal('hide');
                    this.aplicarPreciosEnVistaPrincipal();
                }
            }).always(() => {
                $(e.currentTarget).prop('disabled', false);
            }).fail(() => {
                Util.notify('error', 'Hubo un error al aplicar los precios. Por favor actualice la página e intente de nuevo');
            })
        })
    }

    mostrarFilasTablaEmpresas(tipo, filas) {
        let contenido = '';
        for (let indice in filas) {
            contenido += `<tr>
            <td>
               ${filas[indice].empresa.semaforo} ${filas[indice].empresa.empresa}
            </td>
            <td class="text-center estado">${filas[indice].estado}${filas[indice].restringir ? '<div><strong class="text-danger">RESTRINGIR</strong></div>' : ''}</td>
            <td class="text-center precio-base" data-tc="${filas[indice].tipo_cambio}" data-base="${filas[indice].precio_unitario_base}">${filas[indice].moneda_ofertada} ${Util.formatoNumero(filas[indice].precio_unitario_base, 2)}</td>
            <td class="text-center">${(filas[indice].estado == 'PENDIENTE' && filas[indice].restringir != 1) ? '<input type="checkbox" name="seleccionado[]" value="1"></td>' : '-'}</td>
            <td>`;
            if (filas[indice].estado == 'PENDIENTE' && filas[indice].restringir != 1) {
                contenido += `<div class="input-group">
                    <input type="text" class="form-control input-sm text-center decimal margen" placeholder="Margen" value="10">
                    <span class="input-group-addon">%</span>
                </div>
                <input type="hidden" name="codigo[]" value="${filas[indice].nro_proforma}"><input type="hidden" name="tipo[]" value="${tipo}">`;
            }
            else {
                contenido += '<div class="text-center">-</div>';
            }
            contenido += '</td>';
            if (filas[indice].estado == 'PENDIENTE' && filas[indice].restringir != 1) {
                contenido += `
                <td><input type="text" name="precio[]" placeholder="Precio" class="form-control text-center input-sm precio decimal" value="${filas[indice].precio_publicar}"></td>
                <td><input type="text" name="flete[]" placeholder="Flete" class="form-control text-center input-sm flete decimal" value="${filas[indice].costo_envio_publicar==null ? '0.00' : filas[indice].costo_envio_publicar}"></td>`;
            }
            else {
                contenido += `<td class="text-center">${filas[indice].precio_publicar}</td>
                <td class="text-center">${filas[indice].costo_envio_publicar == null ? '' : filas[indice].costo_envio_publicar}</td>`;
            }
            contenido += `<td class="text-center excedente"></td>`;
            contenido += `</tr>`;
        }
        $('#tbodyCalculadoraEmpresas').html(contenido);
        $('#tbodyCalculadoraEmpresas').find('input[type=checkbox]').trigger('change');
    }

    eliminarFilaEvent() {
        $('#tbodyCalculadoraProducto').on('click', 'button.eliminar', (e) => {
            if (confirm("¿Confirma que desea eliminar la fila?")) {
                $(e.currentTarget).prop('disabled', true);
                this.model.eliminarFila($(e.currentTarget).data('id')).then((data) => {
                    if (data.tipo == 'success') {
                        const obj = this;
                        $(e.currentTarget).closest('tr').fadeOut(300, function () {
                            $(this).remove();
                            obj.calcularCostoTotal();
                            obj.calcularMargenGanacia();
                        })
                    }
                    else {
                        Util.notify(data.tipo, data.mensaje);
                    }
                }).fail(() => {
                    Util.notify('error', 'Hubo un problema al eliminar la fila. Por favor actualice la página e intente de nuevo');
                }).always(() => {
                    $(e.currentTarget).prop('disabled', false);
                })
            }
        })
    }

    seleccionarFilasCalculadoraEvent() {
        $('#tbodyCalculadoraEmpresas').on('change', 'input[type=checkbox]', (e) => {
            $(e.currentTarget).closest('tr').find('input[type=text],input[type=hidden]').prop('disabled', !$(e.currentTarget).is(':checked'));
        })
    }

    actualizarCampoCostoEvent() {
        $('#tbodyCalculadoraProducto').on('change', 'input', (e) => {
            const $el = $(e.currentTarget);
            $el.toggleClass('warning');
            $el.removeClass('danger');
            this.model.actualizarCampo($el.data('id'), $el.data('campo'), $el.val(), $el.data('proforma'), $el.data('tipo')).then((data) => {
                if (data.tipo != 'success') {
                    Util.notify(data.tipo, data.mensaje);
                    $el.addClass('danger');
                }
            }).always(() => {
                $el.toggleClass('warning');
            }).fail(() => {
                $el.addClass('danger');
                Util.notify('error', 'Hubo un problema al actualizar el campo. Por favor actualice la página e intente de nuevo');
            });
        })
    }

    mostrarFilasTablaCostos(cabecera, filas, monedaProforma, nroProforma, tipoProforma) {
        let contenido = `<tr>
        <td colspan="2">Flete</td>
        <td>
            <div class="input-group">
                <span class="input-group-addon">${monedaProforma}</span>
                <input data-id="${cabecera.id_producto}" data-campo="flete" data-proforma="${nroProforma}" data-tipo="${tipoProforma}" type="text" class="form-control text-right input-sm flete decimal" placeholder="Flete" value="${cabecera.flete}">
            </div>
        </td>
        </tr>`;
        for (let indice in filas) {
            contenido += `<tr>
                <td colspan="2">
                    <input data-id="${filas[indice].id}" data-campo="concepto" data-proforma="${nroProforma}" data-tipo="${tipoProforma}" type="text" class="form-control input-sm" placeholder="Concepto" value="${filas[indice].concepto}">
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">${monedaProforma}</span>
                        <input data-id="${filas[indice].id}" data-campo="monto" data-proforma="${nroProforma}" data-tipo="${tipoProforma}" type="text" class="form-control text-right input-sm costo decimal" placeholder="Costo" value="${filas[indice].monto}">
                    </div>
                </td>
                <td class="text-center">
                    <button data-id="${filas[indice].id}" class="btn btn-xs btn-danger eliminar"><span class="glyphicon glyphicon-remove"></span></button>
                </td>
            </tr>`;
        }
        $('#tbodyCalculadoraProducto').html(contenido);
    }
}
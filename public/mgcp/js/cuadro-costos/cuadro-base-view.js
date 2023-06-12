class CuadroBaseView {
    constructor(id, model) {
        this.id = id;
        this.model = model;
    }

    actualizarCampo = ($elemento) => {
        let valor;
        if ($elemento.is('td')) {
            valor = $elemento.html();
        }
        if ($elemento.is('input') || $elemento.is('select') || $elemento.is('radio')) {
            valor = $elemento.val();
        }
        this.model.actualizarCampo($elemento.data('id'), $elemento.data('campo'), valor).then((data) => {
            if (data.tipo == 'success') {
                this.obtenerDetallesFilas();
            }
            Util.notify(data.tipo, data.mensaje);
        });
    }

    obtenerDetallesFilas = () => {
        this.model.obtenerDetallesFilas(this.id).then((respuesta) => {
            //Cuadro bienes para acuerdo marco
            $('#tableCcAm tbody').find('tr').each((index, element) => {
                $(element).find('td.proveedor-nombre').html(respuesta.data.cuadroAm.filas[index].proveedor.nombre);
                $(element).find('td.proveedor-precio').html(respuesta.data.cuadroAm.filas[index].proveedor.precio_format);
                $(element).find('td.proveedor-plazo').html(respuesta.data.cuadroAm.filas[index].proveedor.plazo);
                $(element).find('td.proveedor-flete').html(respuesta.data.cuadroAm.filas[index].proveedor.flete_format);
                $(element).find('td.proveedor-fondo').html(respuesta.data.cuadroAm.filas[index].proveedor.fondo_format);
                $(element).find('td.costo-total').html(respuesta.data.cuadroAm.filas[index].costo_compra_format);
                $(element).find('td.costo-total-convertido').html(respuesta.data.cuadroAm.filas[index].costo_compra_convertido_format);
                $(element).find('td.flete-total').html(respuesta.data.cuadroAm.filas[index].total_flete_format);
                $(element).find('td.costo-flete-total').html(respuesta.data.cuadroAm.filas[index].costo_compra_mas_flete_format);
                $(element).find('td.monto-adjudicado').html(respuesta.data.cuadroAm.filas[index].monto_adjudicado_format);
                $(element).find('td.ganancia').html(respuesta.data.cuadroAm.filas[index].ganancia_format);
                const $botonTransformacion = $(element).find('button.transformacion');
                if (respuesta.data.cuadroAm.filas[index].tiene_transformacion) {
                    $botonTransformacion.removeClass('btn-default').addClass('btn-warning');
                }
                else {
                    $botonTransformacion.removeClass('btn-warning').addClass('btn-default');
                }
            });

            const $ccAmFooter = $('#tableCcAm tfoot');
            $ccAmFooter.find('td.costo-compra-convertido').html(respuesta.data.cuadroAm.suma_costo_compra_convertido_format);
            $ccAmFooter.find('td.flete').html(respuesta.data.cuadroAm.suma_total_flete_format);
            $ccAmFooter.find('td.costo-compra-mas-flete').html(respuesta.data.cuadroAm.suma_costo_compra_mas_flete_format);
            $ccAmFooter.find('td.monto-adjudicado').html(respuesta.data.cuadroAm.suma_monto_adjudicado_format);
            $ccAmFooter.find('td.ganancia-total').html(respuesta.data.cuadroAm.suma_ganancia_format);
            $ccAmFooter.find('td.bienes-servicio').html('-' + respuesta.data.cuadroBs.suma_costo_compra_mas_flete_format);
            $ccAmFooter.find('td.gastos-generales').html('-' + respuesta.data.cuadroGg.suma_total_format);
            $ccAmFooter.find('td.ganancia-real').html(`<strong>${respuesta.data.cuadroAm.ganancia_real_format}</strong>`);
            $ccAmFooter.find('td.margen-ganancia').html(`<strong>${respuesta.data.cuadroAm.margen_ganancia_format}</strong>`);
            $ccAmFooter.find('td.monto-adjudicado-mas-igv').html(`<strong>${respuesta.data.cuadroAm.monto_adjudicado_mas_igv_format}</strong>`);
            $ccAmFooter.find('td.condicion-credito').html(respuesta.data.condicion_credito);
            //Cuadro bienes para servicio
            $('#tableCcBs tbody').find('tr').each((index, element) => {
                $(element).find('td.proveedor-nombre').html(respuesta.data.cuadroBs.filas[index].proveedor.nombre);
                $(element).find('td.proveedor-precio').html(respuesta.data.cuadroBs.filas[index].proveedor.precio_format);
                $(element).find('td.proveedor-plazo').html(respuesta.data.cuadroBs.filas[index].proveedor.plazo);
                $(element).find('td.proveedor-flete').html(respuesta.data.cuadroBs.filas[index].proveedor.flete_format);
                $(element).find('td.proveedor-fondo').html(respuesta.data.cuadroBs.filas[index].proveedor.fondo_format);
                $(element).find('td.costo-total').html(respuesta.data.cuadroBs.filas[index].costo_compra_format);
                $(element).find('td.costo-total-convertido').html(respuesta.data.cuadroBs.filas[index].costo_compra_convertido_format);
                $(element).find('td.flete-total').html(respuesta.data.cuadroBs.filas[index].total_flete_format);
                $(element).find('td.costo-flete-total').html(respuesta.data.cuadroBs.filas[index].costo_compra_mas_flete_format);
            });
            const $ccBsFooter = $('#tableCcBs tfoot');
            $ccBsFooter.find('td.costo-compra-convertido').html(respuesta.data.cuadroBs.suma_costo_compra_convertido_format);
            $ccBsFooter.find('td.flete').html(respuesta.data.cuadroBs.suma_total_flete_format);
            $ccBsFooter.find('td.costo-compra-mas-flete').html(respuesta.data.cuadroBs.suma_costo_compra_mas_flete_format);
            //Cuadro gastos generales
            $('#tableCcGg tbody').find('tr').each((index, element) => {
                $(element).find('td.subtotal').html(respuesta.data.cuadroGg.filas[index].subtotal_format);
                $(element).find('td.otros').html(respuesta.data.cuadroGg.filas[index].otros_format);
                $(element).find('td.total').html(respuesta.data.cuadroGg.filas[index].total_format);
            });
            const $ccGgFooter = $('#tableCcGg tfoot');
            $ccGgFooter.find('td.subtotal').html(respuesta.data.cuadroGg.suma_subtotal_format);
            $ccGgFooter.find('td.otros').html(respuesta.data.cuadroGg.suma_otros_format);
            $ccGgFooter.find('td.total').html(respuesta.data.cuadroGg.suma_total_format);

        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Problema al procesar su solicitud',
                text: 'Por favor actualice la página e intente de nuevo'
            })
        });
    }
}
class CalculadoraModel {
    constructor(token) {
        this.token = token;
    }

    listar = (tipoProforma, requerimiento, proforma, idProducto, empresas, estadoProformas, nroProforma) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.calculadora-producto.listar'),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: tipoProforma, requerimiento: requerimiento, proforma: proforma, idProducto: idProducto, empresas: empresas, estado: estadoProformas, nroProforma: nroProforma, _token: this.token }
        });
    }

    agregarFila = (idProducto, nroProforma, tipoProforma) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.calculadora-producto.agregar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idProducto: idProducto, nroProforma: nroProforma, tipoProforma: tipoProforma, _token: this.token }
        });
    }

    eliminarFila = (id) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.calculadora-producto.eliminar-fila'),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    aplicarPreciosProformas = (data) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.calculadora-producto.aplicar-precios-proformas'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    actualizarCampo = (id, campo, valor, proforma, tipoProforma) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.calculadora-producto.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, proforma: proforma, tipoProforma: tipoProforma, _token: this.token }
        });
    }
}
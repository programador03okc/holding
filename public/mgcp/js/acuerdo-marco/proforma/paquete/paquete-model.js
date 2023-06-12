class PaqueteModel {

    constructor(token, tipoProforma) {
        this.token = token;
        this.tipoProforma = tipoProforma;
    }

    obtenerProformas(filtros) {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.paquete.obtener-proformas`),
            type: 'post',
            dataType: 'json',
            data: filtros
        });
    }

    actualizarSeleccion(id, seleccionado) {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.paquete.actualizar-seleccion`),
            type: 'post',
            dataType: 'json',
            data: { id: id, seleccionado: seleccionado, _token: this.token }
        });
    }

    actualizarPrecio(id, precio) {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.paquete.actualizar-precio`),
            type: 'post',
            dataType: 'json',
            data: { id: id, precio: precio, _token: this.token }
        });
    }

    actualizarCostoEnvio(requerimientoEntrega, proforma, idEmpresa, costo) {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.paquete.actualizar-costo-envio`),
            type: 'post',
            dataType: 'json',
            data: { requerimientoEntrega: requerimientoEntrega, proforma: proforma, idEmpresa: idEmpresa, costo: costo, _token: this.token }
        });
    }

    obtenerListaParaEnviarPortal = () => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.paquete.obtener-lista-para-enviar-portal`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma, _token: this.token }
        });
    }

    enviarCotizacionPortal = (idRequerimiento) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.paquete.enviar-cotizacion-portal`),
            type: 'post',
            dataType: 'json',
            data: { idRequerimiento: idRequerimiento, _token: this.token }
        });
    }
    /*obtenerCantidadPendientes = (id) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.paquete.cantidad-pendientes'),
            type: 'post',
            dataType: 'json',
            data: {  _token: this.token }
        });
    }*/
}
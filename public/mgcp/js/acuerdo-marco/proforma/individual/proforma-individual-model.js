export default class ProformaIndividualModel {

    constructor(token, tipoProforma) {
        this.token=token;
        this.tipoProforma=tipoProforma;
    }

    obtenerListaParaEnviarPortal = () => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.obtener-lista-para-enviar-portal`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma,_token: this.token }
        });
    }

    enviarCotizacionPortal = (idProforma) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.enviar-cotizacion-portal`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma,idProforma: idProforma, _token: this.token }
        });
    }

    actualizarCampo = (id, campo, valor) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.actualizar-campo`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma,id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    actualizarRestringir = (id, valor) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.actualizar-restringir`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma,id: id, valor: valor, _token: this.token }
        });
    }

    deshacerCotizacion = (id) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.deshacer-cotizacion`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma,id: id, _token: this.token }
        });
    }

    obtenerDetalles = (id) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.obtener-detalles`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma,id: id, _token: this.token }
        });
    }
    
    obtenerComentarios = (idProforma) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.obtener-comentarios'),
            type: 'post',
            dataType: 'json',
            data: { idProforma: idProforma, tipoProforma: this.tipoProforma, _token: this.token }
        });
    }

    registrarComentario = (idProforma, comentario) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.registrar-comentario'),
            type: 'post',
            data: { idProforma: idProforma, tipoProforma: this.tipoProforma, comentario: comentario, _token: this.token }
        });
    }

    buscarProducto = (partno) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.busqueda-producto'),
            type: 'POST',
            data: { partno: partno, _token: this.token },
        });
    }

    buscarTipoCambio = (fecha) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ajustes.tipo-cambio.obtener-tc'),
            type: 'post',
            data: { fecha: fecha, _token: this.token },
        });
    }

    obtenerAnalisis = (idProforma) => {
        return $.ajax({
            url : route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.nueva-vista.listar'),
            type: 'post',
            dataType: 'json',
            data: { idProforma: idProforma, _token: this.token }
        });
    }

    registrarAnalisis = (formulario) => {
        return $.ajax({
            url : route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.nueva-vista.registrar'),
            type: 'post',
            dataType: 'json',
            data: formulario
        });
    }

    actualizarProbabilidad = (idProforma, proforma, producto, valor) => {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.actualizar-probabilidad`),
            type: 'post',
            dataType: 'json',
            data: { tipoProforma: this.tipoProforma, idProforma: idProforma, proforma: proforma, producto: producto, valor: valor, _token: this.token }
        });
    }
}
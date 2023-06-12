class CuadroCostoModel extends CuadroBaseModel {
    constructor(token) {
        super(token);
    }

    actualizarCampo = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    actualizarCondicionCredito = (id, tipo, dato) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.actualizar-condicion-credito'),
            type: 'post',
            dataType: 'json',
            data: { id: id, tipo: tipo, dato: dato, _token: this.token }
        });
    }

    seleccionarCentroCosto(idCuadro, idCentro) {
        return $.ajax({
            url: route('mgcp.cuadro-costos.seleccionar-centro-costo'),
            type: 'post',
            dataType: 'json',
            data: { idCentro: idCentro, idCuadro: idCuadro, _token: this.token }
        });
    }

    finalizarCuadro = (idCuadro) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.finalizar'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, _token: this.token }
        });
    }

    enviarOrdenDespacho = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.enviar-orden-despacho'),
            type: 'post',
            processData: false,
            contentType: false,
            dataType: 'json',
            data: data
        });
    }

    actualizarFiltros = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.actualizar-filtros'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }
}
class DespachoModel {
    constructor(token) {
        this.token = token;
    }

    obtenerDetalles = (id, tipo) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.despachos.obtener-detalles'),
            type: 'post',
            dataType: 'json',
            data: { id: id, tipo: tipo, _token: this.token }
        });
    }

    actualizar = (data) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.despachos.actualizar'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }
}
class HistorialProductoModel {

    constructor(token) {
        this.token = token;
    }

    obtenerHistorialActualizaciones = (idProducto, idEmpresa) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.historial-actualizaciones.obtener-historial-producto'),
            type: 'post',
            dataType: 'json',
            data: { idProducto: idProducto, idEmpresa: idEmpresa, _token: this.token }
        });
    }
}
class ProductoModel {

    constructor(token) {
        this.token = token;
    }

    obtenerDetallesPorMMN = (marca, modelo, nroParte) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.obtener-detalles-por-mmn'),
            type: 'post',
            dataType: 'json',
            data: { marca: marca, modelo: modelo, nro_parte: nroParte, _token: this.token },
        });
    }

    obtenerDetallesPorId = (idProducto) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.obtener-detalles-por-id'),
            type: 'post',
            dataType: 'json',
            data: { idProducto: idProducto, _token: this.token },
        });
    }

    obtenerPrecioStockPortal = (tipo, idEmpresa, idProducto) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.obtener-precio-stock-portal'),
            type: 'post',
            dataType: 'json',
            data: { tipo: tipo, idEmpresa: idEmpresa, idProducto: idProducto, _token: this.token }
        });
    }

    actualizarPrecioStockPortal = (idProducto, idEmpresa, tipoActualizacion, valorActual, nuevoValor, comentario) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.actualizar-precio-stock-portal'),
            type: 'post',
            dataType: 'json',
            data: {
                idProducto: idProducto, idEmpresa: idEmpresa, valorActual: valorActual,
                tipoActualizacion: tipoActualizacion, nuevoValor: nuevoValor,
                comentario: comentario, _token: this.token
            }
        });
    }

    actualizarFiltros = (data) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.actualizar-filtros'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    actualizarEstadoStock = (idProducto, valor) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.productos.actualizar-estado-stock'),
            type: 'post',
            dataType: 'json',
            data: {idProducto: idProducto, valor: valor, _token: this.token }
        });
    }

    importarProductos = (data) => {
        return $.ajax({
            url: route('mgcp.integraciones.ceam.productos.importar'),
            type: 'post',
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            data: data
        });
    }
}

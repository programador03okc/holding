class OrdenCompraPublicaModel {
    constructor(token) {
        this.token = token;
    }

    obtenerOrdenesPorMMN = (marca, modelo, nroParte, ocultarSinFecha) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.publicas.obtener-ordenes-por-producto'),
            type: 'post',
            dataType: 'json',
            data: { marca: marca, modelo: modelo, nro_parte: nroParte, ocultarFecha: ocultarSinFecha, _token: this.token }
        });
    }

    obtenerEstadosPortal=(id)=> {
        return $.ajax({
            url: route('mgcp.ordenes-compra.publicas.obtener-estados-portal'),
            type: 'post',
            //dataType: 'json',
            data: { idOrden: id, _token: this.token }
        });
    }

    actualizarFiltros = (data) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.publicas.actualizar-filtros'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }
}
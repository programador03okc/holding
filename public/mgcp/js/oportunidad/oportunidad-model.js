class OportunidadModel {
    constructor(token) {
        this.token = token;
    }

    crearOportunidadDesdeOc = (idOc, tipo, descripcion, idResponsable) => {
        return $.ajax({
            url: route('mgcp.oportunidades.crear-desde-oc-propia'),
            type: 'post',
            dataType: 'json',
            data: { idOc: idOc, tipo: tipo, descripcion: descripcion, responsable: idResponsable, _token: this.token }
        });
    }

    obtenerDetalles = (id) => {
        return $.ajax({
            url: route('mgcp.oportunidades.obtener-detalles'),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    obtenerArchivos = (id, tipo) => {
        return $.ajax({
            url: route('mgcp.oportunidades.obtener-archivos'),
            type: 'post',
            dataType: 'json',
            data: { id: id,tipo: tipo, _token: this.token }
        });
    }

    actualizar = (data) => {
        return $.ajax({
            url: route('mgcp.oportunidades.actualizar'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    eliminar= (id) => {
        return $.ajax({
            url: route('mgcp.oportunidades.eliminar'),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    actualizarFiltros = (data) => {
        return $.ajax({
            url: route('mgcp.oportunidades.actualizar-filtros'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }
}
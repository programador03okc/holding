class NotificacionAmModel {
    constructor(token) {
        this.token = token;
    }

    actualizarLista = () => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.notificaciones.actualizar-lista', {tipo: 1}),
            type: 'get',
            dataType: 'json',
            data: { _token: this.token }
        });
    }

    obtenerFechasDescarga = () => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.notificaciones.obtener-fechas-descarga'),
            type: 'post',
            data: { _token: this.token }
        });
    }

    obtenerDetallesNotificacion = (id) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.notificaciones.obtener-detalles-notificacion'),
            type: 'post',
            data: { id: id, _token: this.token }
        });
    }

    obtenerHistorialNotificacion = (id) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.notificaciones.obtener-historial-notificacion'),
            type: 'post',
            data: { id: id, _token: this.token }
        });
    }
}
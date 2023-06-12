class TransformacionModel {
    constructor(token) {
        this.token=token;
    }

    obtenerDetalles = (idFila) => {
        return $.ajax({
            url: route("mgcp.cuadro-costos.ccam.transformacion.obtener-detalles"),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    eliminarFila = (id) => {
        return $.ajax({
            url: route("mgcp.cuadro-costos.ccam.transformacion.eliminar-fila"),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    agregarFila = (id) => {
        return $.ajax({
            url: route("mgcp.cuadro-costos.ccam.transformacion.agregar-fila"),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    actualizarFila = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.transformacion.actualizar-fila'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }
}
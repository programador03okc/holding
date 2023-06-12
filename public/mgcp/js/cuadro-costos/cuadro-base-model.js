class CuadroBaseModel {
    constructor(token) {
        this.token = token;
    }

    obtenerDetallesFilas = (idCuadro) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.obtener-detalles-filas'),
            type: 'post',
            dataType: 'json',
            data: { id: idCuadro, _token: this.token }
        });
    }
}
class OcPropiaIndicadorModel {
    constructor(token) {
        this.token = token;
    }

    obtenerIndicadorDiario = () => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.indicadores.obtener-indicador-diario'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }

    obtenerIndicadorMensual = () => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.indicadores.obtener-indicador-mensual'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }
}
class DashboardModel {
    constructor(token) {
        this.token = token;
    }

    obtenerIndicadoresCdpPorPeriodo = (anio) => {
        return $.ajax({
            url: route('mgcp.indicadores.dashboard.obtener-indicadores-cdp-por-periodo'),
            type: 'post',
            dataType: 'json',
            data: { anio: anio, _token: this.token }
        });
    }

    obtenerMontosAdjudicadosOcPorAnio = (anio) => {
        return $.ajax({
            url: route('mgcp.indicadores.dashboard.obtener-montos-adjudicados-ordenes-por-anio'),
            type: 'post',
            dataType: 'json',
            data: { anio: anio, _token: this.token }
        });
    }

    obtenerMontosFacturadosTercerosPorAnio = (anio) => {
        return $.ajax({
            url: route('mgcp.indicadores.dashboard.obtener-montos-facturados-terceros-por-anio'),
            type: 'post',
            dataType: 'json',
            data: { anio: anio, _token: this.token }
        });
    }
}
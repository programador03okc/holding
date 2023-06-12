class CentroCostoModel {

    constructor(token) {
        this.token = token;
    }

    obtenerLista() {
        return $.ajax({
            url: route('finanzas.centro-costos.mostrar'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }
}
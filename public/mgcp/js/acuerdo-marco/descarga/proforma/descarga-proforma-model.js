class DescargaProformaModel {

    constructor(token) {
        this.token = token;
    }

    obtenerFechasUltimaDescarga = () =>{
        return $.ajax({
            url: route('mgcp.acuerdo-marco.descargar.proformas.obtener-fechas-ultima-descarga'),
            type: 'post',
            data: { _token: this.token }
        });
    }
}
class SolicitudModel extends CuadroBaseModel {
    constructor(token) {
        super(token);
    }

    enviar = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.solicitudes.nueva'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    responder = (idCuadro, aprobar, comentario) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.solicitudes.responder'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, aprobar: aprobar, comentario: comentario, _token: this.token }
        });
    }

    listar = (idCuadro) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.solicitudes.listar'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, _token: this.token }
        });
    }

    solicitudPrevia = (idCuadro, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.solicitudes.solicitud-previa'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, valor: valor, _token: this.token }
        });
    }

    consultaSolicitudPrevia = (idCuadro) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.solicitudes.consulta-solicitud-previa'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, _token: this.token }
        });
    }
}
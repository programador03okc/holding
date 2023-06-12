class ComentarioOcModel {
    constructor(token) {
        this.token = token;
    }

    listarPorOc = (id, tipo) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.comentarios.listar-por-oc'),
            type: 'post',
            dataType: 'json',
            data: { idOc: id, tipo: tipo, _token: this.token }
        });
    }

    registrarComentario = (idOc, tipo, comentario) => {
        return $.ajax({
            url: route('mgcp.ordenes-compra.propias.comentarios.registrar'),
            type: 'post',
            data: { idOc: idOc, tipo: tipo, comentario: comentario, _token: this.token }
        });
    }
}
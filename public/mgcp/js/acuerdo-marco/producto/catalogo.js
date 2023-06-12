class Catalogo {
    constructor(token) {
        this.token = token;
    }

    listarPorAcuerdo = (idAcuerdo, tipoId, ruta) => {
        return $.ajax({
            url: ruta
            , type: 'post'
            , dataType: 'json'
            , data: { idAcuerdo: idAcuerdo, tipoId: tipoId, _token: this.token }
        });
    }
}
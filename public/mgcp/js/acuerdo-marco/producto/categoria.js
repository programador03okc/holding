class Categoria {
    constructor(token) {
        this.token = token;
    }

    listarPorCatalogo = (idCatalogo, tipoId, ruta) => {
        return $.ajax({
            url: ruta
            , type: 'post'
            , dataType: 'json'
            , data: { idCatalogo: idCatalogo, tipoId: tipoId, _token: this.token }
        });
    }
}
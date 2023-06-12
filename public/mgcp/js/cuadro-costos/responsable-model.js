class ResponsableModel {
    constructor(token) {
        this.token = token;
    }

    agregar = (idCuadro) => {
        return $.ajax({
            url: route("mgcp.cuadro-costos.responsables.agregar"),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, _token: this.token }
        });
    }

    eliminar = (id) => {
        return $.ajax({
            url: route("mgcp.cuadro-costos.responsables.eliminar"),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    actualizar = (id, idCorporativo, porcentaje) => {
        return $.ajax({
            url: route("mgcp.cuadro-costos.responsables.actualizar"),
            type: 'post',
            dataType: 'json',
            data: { id: id, idCorporativo: idCorporativo, porcentaje: porcentaje, _token: this.token }
        });
    }
}
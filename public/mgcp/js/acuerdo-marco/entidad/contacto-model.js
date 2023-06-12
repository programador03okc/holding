class ContactoEntidadModel {

    constructor(token) {
        this.token = token;
    }

    listar = (idEntidad, idOrden, tipoOrden) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.entidades.contactos.listar'),
            type: 'post',
            dataType: 'json',
            data: { idEntidad: idEntidad, idOrdenCompra: idOrden, tipoOrden: tipoOrden, _token: this.token }
        });
    }

    eliminar = (idContacto) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.entidades.contactos.eliminar'),
            type: 'post',
            dataType: 'json',
            data: { idContacto: idContacto, _token: this.token }
        });
    }

    obtenerDetalles = (idContacto) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.entidades.contactos.obtener-detalles'),
            type: 'post',
            dataType: 'json',
            data: { idContacto: idContacto, _token: this.token }
        });
    }

    agregar = (data) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.entidades.contactos.agregar'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    actualizar = (data) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.entidades.contactos.actualizar'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }
}
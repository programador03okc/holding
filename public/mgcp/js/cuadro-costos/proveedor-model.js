class ProveedorModel {

    constructor(token) {
        this.token = token;
    }

    registrar = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.proveedores.registrar'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }
}
class FondoProveedorModel {
    constructor(token) {
        this.token = token;
    }

    /*actualizarCampo = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }*/

    registrarFondo = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.registrar-fondo'),
            type: 'post',
            dataType: 'json',
            data: data
        });
    }

    registrarIngreso = (id, cantidad) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.registrar-ingreso'),
            type: 'post',
            dataType: 'json',
            data: { idFondoProveedor: id, cantidad: cantidad, _token: this.token }
        });
    }

    cambiarEstado = (idFondo) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.cambiar-estado'),
            type: 'post',
            dataType: 'json',
            data: { idFondo: idFondo, _token: this.token }
        });
    }

    obtenerFondosDisponibles = () => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ajustes.fondos-proveedores.obtener-fondos-disponibles'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }
}
class AcuerdoMarcoModel extends CuadroBaseModel {

    constructor(token) {
        super(token);
    }

    agregarFila = (idCuadro, tipoFila, idLicencia, idFondo, moneda = null) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.agregar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, tipoFila: tipoFila, idLicencia: idLicencia, idFondo: idFondo, moneda: moneda, _token: this.token }
        });
    }

    obtenerLicencias = () => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.obtener-licencias'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }

    obtenerFondosMS = () => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.obtener-fondos-ms'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }

    obtenerDetallesFila = (id) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.obtener-detalles-fila'),
            type: 'post',
            dataType: 'json',
            data: { id: id, _token: this.token }
        });
    }

    actualizarCampoFila = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.actualizar-campo-fila'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    obtenerProveedoresFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.obtener-proveedores-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    obtenerHistorialPrecios = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.obtener-historial-precios'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    actualizarCampo = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    actualizarCampoProveedor = (idFila, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.actualizar-campo-proveedor'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, campo: campo, valor: valor, _token: this.token }
        });
    }

    seleccionarProveedor = (idFilaProveedor, idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.seleccionar-proveedor-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFilaProveedor: idFilaProveedor, idFila: idFila, _token: this.token }
        });
    }

    seleccionarMejorPrecio = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.seleccionar-mejor-precio'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    agregarProveedor = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.agregar-proveedor-fila'),
            type: 'post',
            dataType: 'json',
            data: data,
        });
    }

    eliminarFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.eliminar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    registrarComentario = (idFila, comentario) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.registrar-comentario'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, comentario: comentario, _token: this.token }
        });
    }

    actualizarCompraFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.actualizar-compra-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    listarComentarios = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.listar-comentarios'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    eliminarProveedor = (idFilaProveedor, idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.eliminar-proveedor-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFilaProveedor: idFilaProveedor, idFila: idFila, _token: this.token }
        });
    }

    buscarNumeroParte = (idFila, criterio) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccam.buscar-nro-parte'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, criterio: criterio, _token: this.token }
        });
    }
}
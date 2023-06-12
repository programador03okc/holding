class BienServicioModel extends CuadroBaseModel {

    constructor(token) {
        super(token);
    }

    agregarFila = (idCuadro) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.agregar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, _token: this.token }
        });
    }

    actualizarCampoFila = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.actualizar-campo-fila'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    obtenerProveedoresFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.obtener-proveedores-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    obtenerHistorialPrecios=(idFila)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.obtener-historial-precios'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    actualizarCampo = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    actualizarCampoProveedor = (idFila, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.actualizar-campo-proveedor'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, campo: campo, valor: valor, _token: this.token }
        });
    }

    seleccionarProveedor=(idFilaProveedor, idFila)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.seleccionar-proveedor-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFilaProveedor: idFilaProveedor,idFila:idFila, _token: this.token }
        });
    }

    seleccionarMejorPrecio=(idFila)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.seleccionar-mejor-precio'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    agregarProveedor = (data) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.agregar-proveedor-fila'),
            type: 'post',
            dataType: 'json',
            data: data,
        });
    }

    eliminarFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.eliminar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    actualizarCompraFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.actualizar-compra-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }

    /*registrarComentario=(idFila, comentario)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.registrar-comentario'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, comentario: comentario, _token: this.token }
        });
    }*/

    /*listarComentarios=(idFila)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.listar-comentarios'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }*/

    eliminarProveedor=(idFilaProveedor, idFila)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.eliminar-proveedor-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFilaProveedor: idFilaProveedor,idFila:idFila, _token: this.token }
        });
    }

    buscarNumeroParte=(idFila, criterio)=>
    {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccbs.buscar-nro-parte'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila,criterio:criterio, _token: this.token }
        });
    }
}
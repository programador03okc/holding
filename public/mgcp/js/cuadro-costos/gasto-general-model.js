class GastoGeneralModel extends CuadroBaseModel
{
    constructor(token) {
        super(token);
    }

    agregarFila = (idCuadro) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccgg.agregar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idCuadro: idCuadro, _token: this.token }
        });
    }

    actualizarCampoFila = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccgg.actualizar-campo-fila'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }

    actualizarCampo = (id, campo, valor) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccgg.actualizar-campo'),
            type: 'post',
            dataType: 'json',
            data: { id: id, campo: campo, valor: valor, _token: this.token }
        });
    }
    
    eliminarFila = (idFila) => {
        return $.ajax({
            url: route('mgcp.cuadro-costos.ccgg.eliminar-fila'),
            type: 'post',
            dataType: 'json',
            data: { idFila: idFila, _token: this.token }
        });
    }
}
import ProformaNuevaVistaModel from '../../individual/proforma-nueva-vista-model.js'

export default class CONuevaVistaModel extends ProformaNuevaVistaModel {
    constructor(token) {
        super(token,1);
    }

    ingresarFletePorLote = (filtros) => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.nueva-vista.ingresar-flete-por-lote'),
            type: 'post',
            dataType: 'json',
            data: filtros
        });
    }
}
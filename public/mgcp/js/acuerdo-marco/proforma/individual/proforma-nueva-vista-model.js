import ProformaIndividualModel from '../individual/proforma-individual-model.js'

export default class ProformaNuevaVistaModel extends ProformaIndividualModel {

    constructor(token, tipoProforma) {
        super(token, tipoProforma);
    }

    obtenerProformas(filtros) {
        return $.ajax({
            url: route(`mgcp.acuerdo-marco.proformas.individual.${this.tipoProforma==1 ? 'compra-ordinaria' : 'gran-compra'}.nueva-vista.obtener-proformas`),
            type: 'post',
            dataType: 'json',
            data: filtros
        });
    }

    filtrarAnalisisProformas(filtros) {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.individual.filtrar-analisis'),
            type: 'post',
            dataType: 'json',
            data: filtros
        });
    }
}
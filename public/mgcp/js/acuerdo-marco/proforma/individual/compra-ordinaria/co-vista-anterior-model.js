import ProformaIndividualModel from '../../individual/proforma-individual-model.js'

export default class COVistaAnteriorModel extends ProformaIndividualModel {
    constructor(token) {
        super(token,1);
    }

    ingresarFletePorLote = () => {
        return $.ajax({
            url: route('mgcp.acuerdo-marco.proformas.individual.compra-ordinaria.vista-anterior.ingresar-flete-por-lote'),
            type: 'post',
            dataType: 'json',
            data: { _token: this.token }
        });
    }
}
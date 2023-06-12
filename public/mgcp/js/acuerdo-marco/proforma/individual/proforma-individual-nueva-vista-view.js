import ProformaNuevaVistaView from "../proforma-nueva-vista-view.js";

export default class ProformaIndividualNuevaVistaView extends ProformaNuevaVistaView
{
    constructor(model, idUsuario) {
        super(model, idUsuario);
    }
    
    mostrarLugarEntregaEvent()
    {
        $('#divBodyProformas').on('click', 'a.lugar-entrega', (e) => {
            e.preventDefault();
            const $elemento=$(e.currentTarget);
            const $modal=$('#modalLugarEntrega');
            $modal.find('span.requerimiento').html($elemento.data('requerimiento'));
            $modal.find('div.modal-body').html($elemento.data('entrega'));
            $modal.modal('show');
        });
    }
}
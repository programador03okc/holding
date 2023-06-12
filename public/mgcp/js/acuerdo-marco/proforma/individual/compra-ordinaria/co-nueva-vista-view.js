import ProformaIndividualNuevaVistaView from "../proforma-individual-nueva-vista-view.js";

export default class CONuevaVistaView extends ProformaIndividualNuevaVistaView {
    constructor(model, idUsuario) {
        super(model, idUsuario)
    }

    ingresarFletePorLoteEvent = () => {
        //Ingreso de flete masivo

        $('#btnIngresarFletePorLote').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + ' Ingresando');

            this.model.ingresarFletePorLote($('#formFiltros').serialize()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    $('#modalIngresarFletePorLote').modal('hide');
                    this.obtenerProformas();
                }
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Ingresar');
            }).fail(() => {
                Util.notify('error', 'Hubo un error al procesar el flete por lote. Por favor actualice la página e intente de nuevo');
            });
        });
    }
}
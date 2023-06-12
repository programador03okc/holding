class DescargaProformaView {

    constructor(model) {
        this.model = model;
    }

    obtenerFechasUltimaDescargaEvent = () => {

        $('#modalUltimaActualizacionLista').on('show.bs.modal', () => {
            const $modal=$('#modalUltimaActualizacionLista');
            const $mensaje=$modal.find('div.mensaje');
            $('#tbodyUltimaActualizacionLista').html('');
            Util.bloquearConSpinner($mensaje);
            this.model.obtenerFechasUltimaDescarga().then((data) => {
                $('#tbodyUltimaActualizacionLista').html(data);
                Util.liberarBloqueoSpinner($mensaje);
            }).fail(()=>{
                alert("Hubo un problema al obtener la data. Por favor actualice la página e intente de nuevo");
                $('#modalUltimaActualizacionLista').modal('hide');
            });
        });
    }
}
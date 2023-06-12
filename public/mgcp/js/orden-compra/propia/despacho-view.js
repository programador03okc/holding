class DespachoView {
    constructor(model, puedeActualizar) {
        this.model = model;
        this.puedeActualizar = puedeActualizar;
        this.boton = null;
    }

    obtenerDetallesEvent() {
        const $modal = $('#modalActualizarDespacho');

        $('#tableOrdenes').on("click", "button.despacho", (e) => {
            this.boton = $(e.currentTarget);
            $modal.find('input[name=id]').val($(e.currentTarget).data('id'));
            $modal.find('input[name=tipo]').val($(e.currentTarget).data('tipo'));
            $modal.find('span.orden').html($(e.currentTarget).data('orden'));
            //$modal.find(`input[name=despachada][value=${$(e.currentTarget).data('despachada') ? '1' : '0'}]`).prop('checked', true);
            $modal.modal('show');
        });

        $modal.on('shown.bs.modal', () => {
            $modal.find('div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc",
                zIndex: 2000
            });
            this.obtenerDetalles();
        });
    }

    actualizarDespachoEvent() {
        $('#btnActualizarDespacho').on('click', (e) => {
            $(e.currentTarget).prop('disabled', true);
            $(e.currentTarget).html(Util.generarPuntosSvg() + 'Actualizando');
            this.model.actualizar($('#formActualizarDespacho').serialize()).then((data) => {
                if (data.tipo == 'success') {
                    $("#modalActualizarDespacho").modal('hide');
                    if (data.despachada == 1) {
                        this.boton.removeClass('btn-default').addClass('btn-success');
                    } else {
                        this.boton.removeClass('btn-success').addClass('btn-default');
                    }
                }
                Util.notify(data.tipo, data.mensaje);
                console.log(data.mensaje);
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al actualizar el despacho. Por favor actualice la página e intente de nuevo');
            }).always(()=>{
                $(e.currentTarget).prop('disabled', false);
            $(e.currentTarget).html('Actualizar');
            })
        })
    }

    obtenerDetalles() {
        const $modal = $("#modalActualizarDespacho");
        $modal.find('input, select').prop('disabled', true);
        this.model.obtenerDetalles(this.boton.data('id'), this.boton.data('tipo')).then((data) => {
            if (data.id == null) {
                $modal.find('input[name=despachada]').val([0]);
                $modal.find('select[name=transportista]').val(0);
                $modal.find('input[type=text]').val('');
            } else {
                $modal.find('input[name=despachada]').val([1]);
                $modal.find('select[name=transportista]').val(data.id_transportista==null ? 0 : data.id_transportista);
                $modal.find('input[name=fleteReal]').val(data.flete_real);
                $modal.find('input[name=fechaSalida]').val(data.fecha_salida);
                $modal.find('input[name=fechaLlegada]').val(data.fecha_llegada);
            }
            if (this.puedeActualizar == 1) {
                $modal.find('input, select').prop('disabled', false);
            }
            $modal.find('select[name=transportista]').selectpicker('refresh')
        }).fail(() => {
            Util.notify('error', 'Hubo un problema al obtener los detalles del despacho. Por favor actualice la página e intente de nuevo');
            $modal.modal('hide');
        }).always(() => {
            $modal.find('div.modal-body').LoadingOverlay("hide", true);
        })
    }
}
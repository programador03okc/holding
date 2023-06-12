class CuadroProductoView extends CuadroDetalleView {
    constructor(id, model, tabla, proveedorView, comentarioView) {
        super(id, model, tabla);
        this.proveedorView = proveedorView;
        this.comentarioView = comentarioView;
    }

    buscarNroParteEvent = () => {
        let contenedor = "";
        $(this.tabla + ' tbody').on("focus", "td.numero-parte", (e) => {
            contenedor = $(e.currentTarget).html();
        });

        $(this.tabla + ' tbody').on("blur", "td.numero-parte", (e) => {
            if (contenedor !== $(e.currentTarget).html()) {
                buscarNumeroParte($(e.currentTarget));
            }
        });

        const buscarNumeroParte = ($celdaParte) => {
            const $celdaDescripcion = $celdaParte.closest('tr').find('td.descripcion');
            const $celdaMarca = $celdaParte.closest('tr').find('td.marca');
            $celdaParte.removeClass('success').removeClass('danger').addClass('warning');

            this.model.buscarNumeroParte($celdaParte.data('id'), $celdaParte.html()).then((data) => {
                if (data.tipo == 'success') {
                    $celdaDescripcion.html(data.descripcion);
                    $celdaMarca.html(data.marca);
                    $celdaParte.removeClass('warning').addClass('success');
                }
                else {
                    Util.notify(data.tipo, data.mensaje);
                    $celdaParte.removeClass('warning').addClass('danger');
                    $celdaParte.html($celdaParte.html() + 'X');
                }

            }).fail(() => {
                $celdaParte.html($celdaParte.html() + 'X');
                $celdaParte.removeClass('warning').addClass('danger');
            });
        }
    }

    actualizarCompraFilaEvent = () => {
        $(this.tabla + ' tbody').on("click", "button.compra", (e) => {
            const $boton = $(e.currentTarget);
            let comprado = $boton.hasClass('btn-success');

            Swal.fire({
                icon: 'question',
                title: `¿Desea marcar esta fila como${comprado ? ' no' : ''} comprada?`,
                confirmButtonText: 'Sí',
                showDenyButton: true,
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $boton.prop('disabled', true);
                    this.model.actualizarCompraFila($boton.data('id')).then((data) => {
                        if (data.tipo == 'success') {
                            if (comprado) {
                                $boton.removeClass('btn-success').addClass('btn-default');
                            }
                            else {
                                $boton.removeClass('btn-default').addClass('btn-success');
                            }
                        }
                        Util.notify(data.tipo, data.mensaje);
                    }).fail(() => {
                        Util.notify('error', 'Hubo un problema al actualizar. Por favor actualice la página e intente de nuevo');
                    }).always(() => {
                        $boton.prop('disabled', false);
                    });
                }
            })


            /*if (confirm(`¿Desea marcar esta fila como${comprado ? ' no' : ''} comprada?`)) {
                
                
            }*/
        });
    }

    obtenerProveedoresEvent = () => {
        $(this.tabla + ' tbody').on("click", "td.info", (e) => {
            this.proveedorView.cuadroView = this;
            this.proveedorView.obtenerLista($(e.currentTarget));
        });
    }

    listarComentariosEvent = () => {
        $(this.tabla + ' tbody').on("click", "button.comentarios", (e) => {
            this.comentarioView.model = this.model;
            this.comentarioView.obtenerLista($(e.currentTarget));
        });
    }
}
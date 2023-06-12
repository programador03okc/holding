class AcuerdoMarcoView extends CuadroProductoView {
    constructor(id, model, proveedorView, comentarioView) {
        super(id, model, '#tableCcAm, #tableProductoTransformado', proveedorView, comentarioView);
        this.agregarFilaEvent();
        this.actualizarCampoFilaEvent();
        this.actualizarCampoEvent();
        this.eliminarFilaEvent();
        this.buscarNroParteEvent();
        this.actualizarCompraFilaEvent();
        this.obtenerProveedoresEvent();
        this.listarComentariosEvent();
    }

    agregarFilaEvent = () => {
        //Filas que no son licencia
        $('#contenedorCcAm').on('click', 'a.nueva-fila', (e) => {
            e.preventDefault();
            switch ($(e.currentTarget).data('tipo')) {
                case 1: //Producto
                case 3: //Servicio
                    this.agregarFilaProducto($(e.currentTarget).data('tipo'));
                    break;
                case 2: //Licencia
                    $('#modalLicencias').modal('show');
                    //this.listarLicencias();
                break;
                case 4: //Fondo Microsoft
                    $('#modalFondosMS').modal('show');
                break;
            }
        })
        //Fila licencia
        $('#tbodyLicencias').on('click', 'button.seleccionar', (e) => {
            this.agregarFilaLicencia($(e.currentTarget));
        });
        $('#tbodyFondoMS').on('click', 'button.seleccionar', (e) => {
            this.agregarFilaFondoMS($(e.currentTarget));
        });
    }

    listarLicenciasEvent = () => {
        $('#modalLicencias').on('show.bs.modal', () => {
            $('#tbodyLicencias').html('');
        });

        $('#modalLicencias').on('shown.bs.modal', () => {
            $('#modalLicencias').find('div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                imageColor: "#3c8dbc",
                zIndex: 2000
            });

            this.model.obtenerLicencias().then((data) => {
                let contenido = '';
                for (let indice in data.licencias) {
                    contenido += `
                        <tr>
                            <td class="text-center">${data.licencias[indice].marca}</td>
                            <td class="text-center">${data.licencias[indice].part_no}</td>
                            <td>${data.licencias[indice].descripcion}</td>
                            <td class="text-center"><button data-id="${data.licencias[indice].id}" class="btn btn-xs btn-primary seleccionar">Selec.</button></td>
                        </tr>
                        `;
                }
                $('#tbodyLicencias').html(contenido);
            }).always(() => {
                $('#modalLicencias').find('div.modal-body').LoadingOverlay("hide", true);
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al obtener licencias',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
            });
        });
    }

    listarFondoMSEvent = () => {
        $('#modalFondosMS').on('show.bs.modal', () => {
            $('#tbodyFondoMS').html('');
        });

        $('#modalFondosMS').on('shown.bs.modal', () => {
            $('#modalFondosMS').find('div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                imageColor: "#3c8dbc",
                zIndex: 2000
            });

            this.model.obtenerFondosMS().then((data) => {
                let contenido = '';
                for (let indice in data.fondos) {
                    contenido += `
                        <tr>
                            <td class="text-center">MICROSOFT</td>
                            <td class="text-center">${data.fondos[indice].part_no}</td>
                            <td>${data.fondos[indice].descripcion}</td>
                            <td class="text-center"><button data-id="${data.fondos[indice].id}" class="btn btn-xs btn-primary seleccionar">Selec.</button></td>
                        </tr>
                        `;
                }
                $('#tbodyFondoMS').html(contenido);
            }).always(() => {
                $('#modalFondosMS').find('div.modal-body').LoadingOverlay("hide", true);
            }).fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Problema al obtener los fondos',
                    text: 'Por favor actualice la página e intente de nuevo'
                })
            });
        });
    }

    agregarFilaLicencia = ($boton) => {
        $boton.prop('disabled', true);
        this.model.agregarFila(this.id, 2, $boton.data('id'), 0).then((respuesta) => {
            $('#modalLicencias').modal('hide');
            $(this.tabla).find('tbody').append(respuesta.data);
        }).always(() => {
            $boton.prop('disabled', false);
        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Problema al agregar fila',
                text: 'Por favor actualice la página e intente de nuevo'
            })
        });
    }

    agregarFilaFondoMS = ($boton) => {
        $boton.prop('disabled', true);
        var moneda = $("#selectMonedaPvu").val();
        this.model.agregarFila(this.id, 4, 0, $boton.data('id'), moneda).then((respuesta) => {
            $('#modalLicencias').modal('hide');
            this.obtenerDetallesFilas();
            $(this.tabla).find('tbody').append(respuesta.data);
        }).always(() => {
            $boton.prop('disabled', false);
        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Problema al agregar fila',
                text: 'Por favor actualice la página e intente de nuevo'
            })
        });
    }

    agregarFilaProducto = (idTipo) => {
        $('#btnCcAmFila').prop('disabled', true);
        this.model.agregarFila(this.id, idTipo, 0, 0).then((respuesta) => {
            $(this.tabla).find('tbody').append(respuesta.data);
            $(this.tabla).find('tbody').find('tr:last').find('td.success:first').trigger('focus')
        }).always(() => {
            $('#btnCcAmFila').prop('disabled', false);
        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Problema al agregar fila',
                text: 'Por favor actualice la página e intente de nuevo'
            })
        });

    }

    actualizarCampoEvent = () => {

        $('#selectMonedaPvu').on('change', (e) => {
            this.actualizarCampo($(e.currentTarget));
        });

        /*$('#txtCcAmFechaEntrega').on('change', (e) => {
            this.actualizarCampo($(e.currentTarget));
            $('#spanCcAmFechaEntrega').html($(e.currentTarget).val());
        });*/
        /*
        $('#txtPlazoCcAm').on('change', (e) => {
            this.actualizarCampo($(e.currentTarget));
            $('#spanCcAmPlazo').html($(e.currentTarget).val());
        });*/
    }

    /*transformacionEvent = () => {
        $(this.tabla+' tbody').on('click','button.transformacion',(e)=>{
            const $modal=$('#modalTransformacion');
            const $mensajeInicial = $modal.find('div.mensaje-inicial');
            Util.bloquearConSpinner($mensajeInicial);
            $modal.modal('show');
            //$modal.find('td').html('');
            $('#tableProductoTransformado tbody').find('td.success').data('id',$(e.currentTarget).data('id'));
            this.model.obtenerDetallesFila($(e.currentTarget).data('id')).then((data) => {
                const $tablaProductoBase=$('#tableProductoBase');
                $tablaProductoBase.find('td.numero-parte').html(data.fila.part_no);
                $tablaProductoBase.find('td.marca').html(data.fila.marca);
                $tablaProductoBase.find('td.descripcion').html(data.fila.descripcion);
                const $tablaProductoTransformado=$('#tableProductoTransformado');
                $tablaProductoTransformado.find('td.numero-parte').html(data.fila.part_no_producto_transformado);
                $tablaProductoTransformado.find('td.marca').html(data.fila.marca_producto_transformado);
                $tablaProductoTransformado.find('td.descripcion').html(data.fila.descripcion_producto_transformado);
                
                Util.liberarBloqueoSpinner($mensajeInicial);
            }).fail(()=>{
                alert("Hubo un problema al obtener los detalles de la transformación. Por favor actualcie la página e intente de nuevo");
                $modal.modal('hide');
            })
        });

        let contenedor = "";

        $('#tableProductoTransformado tbody').on("focus", "td.success, td.danger", (e) => {
            contenedor = $(e.currentTarget).html();
        });

        $('#tableProductoTransformado tbody').on("blur", "td.success, td.danger", (e) => {
            if (contenedor != $(e.currentTarget).html()) {
                this.actualizarCampoFila($(e.currentTarget));
            }
        });
    }*/
}
class TransformacionView {
    constructor(model, idCuadro, tipoEdicion) {
        this.model = model;
        this.idCuadro = idCuadro;
        this.tipoEdicion = tipoEdicion;
    }

    agregarFilasTablaBase(data) {
        $('#tableProductoBase').find('tbody').html(`
            <tr>
                <td class="text-center">${data.part_no}</td>
                <td class="text-center">${data.marca}</td>
                <td>${data.descripcion}</td>
            </tr>`);
    }

    agregarFilasTablaTransformado(data) {
        $('#tableProductoTransformado').find('tbody').html(`
            <tr>
                <td ${this.tipoEdicion == 'corporativo' ? 'data-id="' + data.id + '" data-campo="part_no_producto_transformado" class="text-center success" contenteditable="true"' : 'class="text-center"'}">${data.part_no_producto_transformado}</td>
                <td ${this.tipoEdicion == 'corporativo' ? 'data-id="' + data.id + '" data-campo="marca_producto_transformado" class="text-center success" contenteditable="true"' : 'class="text-center"'}">${data.marca_producto_transformado}</td>
                <td ${this.tipoEdicion == 'corporativo' ? 'data-id="' + data.id + '" data-campo="descripcion_producto_transformado" class="success" contenteditable="true"' : ''}">${data.descripcion_producto_transformado}</td>
                <td ${this.tipoEdicion == 'corporativo' ? 'data-id="' + data.id + '" data-campo="comentario_producto_transformado" class="success" contenteditable="true"' : ''}">${data.comentario_producto_transformado}</td>
            </tr>`);
    }

    agregarFilasTablaMovimientos(data) {
        //let selectFilasCuadro=;
        let filasMovimientos = '';
        let editable = this.tipoEdicion == 'corporativo';
        for (let indice in data.movimientos) {
            filasMovimientos += `<tr>
            <td ${editable ? 'class="success"' : ''} data-id="${data.movimientos[indice].id}" data-campo="id_fila_ingresa">${this.generarSelectFilasCuadro(data.filasCuadro, data.movimientos[indice].id_fila_ingresa)}</td>
            <td ${editable ? 'contenteditable="true" class="success sale"' : ''}  data-id="${data.movimientos[indice].id}" data-campo="sale">${data.movimientos[indice].sale}</td>
            <td ${editable ? 'contenteditable="true" class="success sale"' : ''}  data-id="${data.movimientos[indice].id}" data-campo="comentario">${data.movimientos[indice].comentario}</td>
            <td class="text-center"><button class="btn btn-xs btn-danger eliminar ${editable ? '' : 'hidden'}" data-id="${data.movimientos[indice].id}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>
            </tr>`;
        }
        $('#tableMovimientosTransformacion').find('tbody').html(filasMovimientos);
    }

    generarSelectFilasCuadro(data, idSeleccionar) {

        if (this.tipoEdicion == 'corporativo') {
            let selectFilasCuadro = '';
            selectFilasCuadro = `<select class="form-control input-sm ingresa">
            <option value="0">(SIN INGRESO)</option>`;
            for (let indice in data) {
                selectFilasCuadro += `<option value="${data[indice].id}" ${idSeleccionar == data[indice].id ? 'selected' : ''}>${data[indice].descripcion}</option>`;
            }
            selectFilasCuadro += '</select>';
            return selectFilasCuadro;
        }
        else {
            for (let indice in data) {
                if (idSeleccionar == data[indice].id) {
                    return data[indice].descripcion;
                }
            }
            return ''; //en caso no se encuentre la fila seleccionada
        }
    }

    seleccionarCheckboxes(data) {
        const $div = $('#divOpcionesAdicionalesTransformacion');
        const editable = !(this.tipoEdicion == 'corporativo');
        $div.find('input[type=checkbox]').data('id', data.id);
        $div.find('input[name=etiquetado]').prop('checked', data.etiquetado_producto_transformado).prop('disabled', editable);
        $div.find('input[name=bios]').prop('checked', data.bios_producto_transformado).prop('disabled', editable);
        $div.find('input[name=officePreinstalado]').prop('checked', data.office_preinstalado_producto_transformado).prop('disabled', editable);
        $div.find('input[name=officeActivado]').prop('checked', data.office_activado_producto_transformado).prop('disabled', editable);
    }

    obtenerDetalles(id) {

        this.model.obtenerDetalles(id).then((data) => {
            this.agregarFilasTablaBase(data.filaCuadro);
            this.agregarFilasTablaTransformado(data.filaCuadro);
            this.seleccionarCheckboxes(data.filaCuadro);
            this.agregarFilasTablaMovimientos(data);

            $('#modalTransformacion').find('div.modal-body').LoadingOverlay("hide", true);
        });
    }

    actualizarCheckboxEvent(cuadroModel) {
        $('#divOpcionesAdicionalesTransformacion').on('click', 'input[type=checkbox]', (e) => {
            const $check = $(e.currentTarget);
            cuadroModel.actualizarCampoFila($check.data('id'), $check.data('campo'), ($check.is(':checked') ? '1' : '0')).then((data) => {
                Util.notify(data.tipo, data.mensaje);
            });
        });
    }

    obtenerDetallesEvent() {
        $('#tableCcAm').on('click', 'button.transformacion', (e) => {
            $('#btnAgregarFilaMovimientoTransformacion').data('id', $(e.currentTarget).data('id'));
            const $modal = $('#modalTransformacion');
            $('#tableProductoBase tbody').html('<tr><td colspan="3" class="text-center">Obteniendo datos...</td></tr>');
            //$('').html('<tr><td colspan="4" class="text-center">Obteniendo datos...</td></tr>');
            $('#tableMovimientosTransformacion tbody, #tableProductoTransformado tbody').html('<tr><td colspan="4" class="text-center">Obteniendo datos...</td></tr>');

            $modal.find('input[type=checkbox]').prop('checked', false);
            $modal.modal('show');
        });

        $("#modalTransformacion").on('shown.bs.modal', () => {
            $('#modalTransformacion').find('div.modal-body').LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc",
                zIndex: 2000
            });
            this.obtenerDetalles($('#btnAgregarFilaMovimientoTransformacion').data('id'));
        });
    }

    actualizarFilaEvent() {
        $('#tableMovimientosTransformacion tbody').on('change', 'select', (e) => {
            const $celda = $(e.currentTarget).closest('td');
            this.actualizarFila($celda.data('id'), $celda.data('campo'), $(e.currentTarget).val(), $celda);
        });

        let contenedor = "";

        $('#tableMovimientosTransformacion tbody').on("focus", "td.success, td.danger", (e) => {
            contenedor = $(e.currentTarget).html();
        });

        $('#tableMovimientosTransformacion tbody').on("blur", "td.success, td.danger", (e) => {
            if (contenedor != $(e.currentTarget).html()) {
                this.actualizarFila($(e.currentTarget).data('id'), $(e.currentTarget).data('campo'), $(e.currentTarget).html(), $(e.currentTarget));
            }
        });
    }

    agregarFilaEvent() {
        $('#btnAgregarFilaMovimientoTransformacion').on('click', (e) => {
            $(e.currentTarget).prop('disabled', true);
            this.model.agregarFila($(e.currentTarget).data('id')).then((data) => {
                if (data.tipo == 'success') {
                    $('#tableMovimientosTransformacion tbody').append(`<tr>
                    <td class="success" data-campo="id_fila_ingresa" data-id="${data.id}">${this.generarSelectFilasCuadro(data.filasCuadro, 0)}</td>
                    <td contenteditable="true" class="success sale" data-campo="sale" data-id="${data.id}"></td>
                    <td contenteditable="true" class="success sale" data-campo="comentario" data-id="${data.id}"></td>
                    <td class="text-center"><button class="btn btn-xs btn-danger eliminar" data-id="${data.id}"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>
                    </tr>`);
                }
                else {
                    Util.notify(data.tipo, data.mensaje);
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al agregar la fila. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $(e.currentTarget).prop('disabled', false);
            });
        })
    }

    eliminarFilaEvent() {
        $('#tableMovimientosTransformacion tbody').on('click', 'button.eliminar', (e) => {
            Swal.fire({
                icon: 'question',
                title: `¿Confirma que desea eliminar la fila?`,
                confirmButtonText: 'Sí',
                showDenyButton: true,
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $(e.currentTarget).prop('disabled', true);
                    this.model.eliminarFila($(e.currentTarget).data('id')).then((data) => {
                        Util.notify(data.tipo, data.mensaje);
                        if (data.tipo == 'success') {
                            $(e.currentTarget).closest('tr').fadeOut(300, function () {
                                $(this).remove();
                            })
                        }
                        else {
                            $(e.currentTarget).prop('disabled', false);
                        }
                    }).fail(() => {
                        $(e.currentTarget).prop('disabled', false);
                    });
                }
            })
        });
    }

    actualizarFila(id, campo, valor, $celda) {
        $celda.removeClass('danger').addClass('warning');
        this.model.actualizarFila(id, campo, valor).then((data) => {
            if (data.tipo == 'success') {
                $celda.addClass('success');
            }
            else {
                $celda.addClass('error');
                Util.notify(data.tipo, data.mensaje);
            }
        }).fail(() => {
            $celda.addClass('danger');
            Util.notify('error', data.mensaje);
        }).always(() => {
            $celda.removeClass('warning');
        });
    }
}
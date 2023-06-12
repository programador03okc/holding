class ProveedorView {
    constructor(tipoEdicion, modelProveedor, fondoProveedorView, cuadroView) {
        this.tipoEdicion = tipoEdicion;
        this.modelProveedor = modelProveedor;
        this.cuadroView = cuadroView;
        this.fondoProveedorView = fondoProveedorView;
        this.nuevoProveedorEvent();
        this.agregarFilaEvent();
        this.seleccionarEvent();
        this.eliminarFilaEvent();
        this.actualizarCampoEvent();
        this.seleccionarMejorPrecioEvent();
        this.historialPreciosEvent();
        this.cambiarMonedaEvent();
    }

    cambiarMonedaEvent = () => {
        $('#tableProveedores').on('click', 'a.moneda', (e) => {
            e.preventDefault();
            $('#spanMonedaSeleccionadaProveedor').html($(e.currentTarget).html());
            $('#txtMonedaProveedor').val($(e.currentTarget).data('moneda'));
            $('#txtPrecio').trigger('focus');
        });
    }

    obtenerLista = ($elemento) => {
        $('#txtProveedorIdFila').val($elemento.data('id'));
        $('#txtPrecio').val('');
        $('#tableProveedoresFila').find('tbody').html('<tr><td colspan="9" class="text-center">Obteniendo datos...</td></tr>');
        $('#btnMejorPrecio').prop('disabled', true);
        $('div.producto').html('-Descripción: ' + $elemento.closest('tr').find('td.descripcion').html());
        $('div.cantidad').html('-Cantidad: ' + $elemento.closest('tr').find('td.cantidad').html());
        this.fondoProveedorView.obtenerFondosDisponibles();
        $('#modalProveedores').modal('show');
        this.cuadroView.model.obtenerProveedoresFila($('#txtProveedorIdFila').val()).then((datos) => {
            let filas = '';
            for (let indice in datos.proveedores) {
                filas += `<tr>
                        <td>${datos.proveedores[indice].proveedor.razon_social}</td>
                        <td data-id="${datos.proveedores[indice].id}" data-campo="plazo" class="text-center entero plazo${this.tipoEdicion == 'ninguno' ? '"' : ' success" contenteditable="true"'}>${datos.proveedores[indice].plazo}</td>
                        <td class="text-center">${datos.proveedores[indice].moneda == 's' ? 'S/' : '$'}</td>
                        <td data-id="${datos.proveedores[indice].id}" data-campo="precio" class="text-right decimal precio${this.tipoEdicion == 'ninguno' ? '"' : ' success" contenteditable="true"'}>${Util.formatoNumero(datos.proveedores[indice].precio, 2)}</td>
                        <td data-id="${datos.proveedores[indice].id}" data-campo="flete" class="text-right decimal flete${this.tipoEdicion == 'ninguno' ? '"' : ' success" contenteditable="true"'}>${Util.formatoNumero(datos.proveedores[indice].flete, 2)}</td>
                        <td>`;
                if (datos.proveedores[indice].fondo_proveedor == null) {
                    filas += 'Ninguno';
                }
                else {
                    filas += `${datos.proveedores[indice].fondo_proveedor.descripcion} (${datos.proveedores[indice].fondo_proveedor.valor_unitario_format})`;
                }
                filas += `</td>
                        <td>${datos.proveedores[indice].comentario}</td>
                        <td class="text-center seleccionado">${datos.proveedores[indice].id == datos.provSeleccionado ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : ''}</td>
                        <td class="text-center">
                        <div class="btn-group" role="group" aria-label="...">`;

                if (this.tipoEdicion != 'ninguno') {
                    filas += `<button data-id="${datos.proveedores[indice].id}" class="btn btn-default btn-xs seleccionar" title="Seleccionar"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                        <button data-id="${datos.proveedores[indice].id}" class="btn btn-default btn-xs eliminar" title="Eliminar fila"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>`;
                }
                filas += `<button data-id="${datos.proveedores[indice].id}" class="btn btn-default btn-xs historial-precios" title="Ver historial de precios"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button>
                    </div>
                    </td>
                    </tr>`;
            }
            $('#tableProveedoresFila').find('tbody').html(filas);
            $('#btnMejorPrecio').prop('disabled', false);
        })
    }

    agregarFilaEvent = () => {
        $('#btnAgregarProveedorCuadro').on('click', (e) => {
            const $botonAgregar = $(e.currentTarget);
            const $precio = $('#txtPrecio');
            const $plazo = $('#txtPlazo');
            const $flete = $('#txtFlete');
            //const $comentario = $('#txtComentario');
            let tipoError = '';
            if ($precio.val() == '') {
                tipoError = 'precio';
                $precio.trigger('focus');
            }
            if ($plazo.val() == '') {
                tipoError = 'plazo';
                $plazo.trigger('focus');
            }
            if ($flete.val() == '') {
                tipoError = 'flete';
                $flete.trigger('focus');
            }
            if (tipoError != '') {
                Swal.fire({
                    icon: 'error',
                    title: `Ingrese un ${tipoError} antes de continuar`
                })
                return false;
            }
            //$botonAgregar.html(Util.generarPuntosSvg() + 'Agregando');
            $botonAgregar.prop('disabled', true);
            this.cuadroView.model.agregarProveedor($('#formProveedor').serialize()).then((resultado) => {
                if (resultado.tipo == 'error') {
                    Swal.fire({
                        icon: resultado.tipo,
                        title: resultado.titulo,
                        text: resultado.texto
                    })
                    return false;
                }
                let fila = `<tr><td>${$('#selectProveedor option:selected').html()}</td>
                <td data-id="${resultado.proveedor.id}" data-campo="plazo" class="text-center success entero plazo" contenteditable="true">${resultado.proveedor.plazo}</td>
                <td class="text-center">${resultado.proveedor.moneda == 's' ? 'S/' : '$'}</td>
                <td data-id="${resultado.proveedor.id}" data-campo="precio" class="text-right success decimal precio" contenteditable="true">${Util.formatoNumero(resultado.proveedor.precio, 2)}</td>
                <td data-id="${resultado.proveedor.id}" data-campo="flete" class="text-right success decimal flete" contenteditable="true">${Util.formatoNumero(resultado.proveedor.flete, 2)}</td>
                <td>`;
                if (resultado.fondo == null) {
                    fila += 'Ninguno';
                }
                else {
                    fila += `${resultado.fondo.descripcion} (${resultado.fondo.valor_unitario_format})`;
                }
                fila += `</td>
                <td>${resultado.proveedor.comentario}</td>
                <td class="text-center seleccionado"></td>
                <td class="text-center">
                <div class="btn-group" role="group" aria-label="...">
                <button data-id="${resultado.proveedor.id}" class="btn btn-default btn-xs seleccionar" title="Seleccionar"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                <button data-id="${resultado.proveedor.id}" class="btn btn-default btn-xs eliminar" title="Eliminar fila"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                <button data-id="${resultado.proveedor.id}" class="btn btn-default btn-xs historial-precios" title="Ver historial de precios"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button>
                </div>
                </td></tr>`;
                $('#tableProveedoresFila').find('tbody').append(fila);
                //$precio.val('');
                //$('#txtComentario').val('');
            }).always(() => {
                //$botonAgregar.html('Agregar');
                $botonAgregar.prop('disabled', false);
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al agregar el proveedor. Por favor actualice la página e intente de nuevo');
            });
        });
    }

    seleccionarEvent = () => {
        $('#tableProveedoresFila tbody').on('click', 'button.seleccionar', (e) => {
            const $elemento = $(e.currentTarget);
            $elemento.prop('disabled', true);
            this.cuadroView.model.seleccionarProveedor($elemento.data('id'), $('#txtProveedorIdFila').val()).then((resultado) => {
                switch (resultado.tipo) {
                    case 'success':
                        $('#tableProveedoresFila tbody').find('button.seleccionar').each(function () {
                            $(this).closest('tr').find('td.seleccionado').html('');
                        });
                        $elemento.closest('tr').find('td.seleccionado').html('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
                        this.cuadroView.obtenerDetallesFilas();
                        this.fondoProveedorView.obtenerFondosDisponibles();
                        break;
                    case 'error':
                        Swal.fire({
                            icon: resultado.tipo,
                            title: resultado.titulo,
                            text: resultado.texto,
                        })
                        break;
                }
            }).always(() => {
                $elemento.prop('disabled', false);
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al seleccionar el precio. Por favor actualice la página e intente de nuevo');
            });
        });
    }

    eliminarFilaEvent() {
        $('#tableProveedoresFila tbody').on('click', 'button.eliminar', (e) => {
            Swal.fire({
                icon: 'question',
                title: `¿Está seguro de eliminar la fila?`,
                confirmButtonText: 'Sí',
                showDenyButton: true,
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    const $elemento = $(e.currentTarget);
                    $elemento.prop('disabled', true);
                    this.cuadroView.model.eliminarProveedor($elemento.data('id'), $('#txtProveedorIdFila').val()).then((data) => {
                        if (data.tipo == 'success') {
                            this.fondoProveedorView.obtenerFondosDisponibles();
                            $('#tableProveedoresFila').find('tr.' + $elemento.data('id')).find('button.cerrar-historial').trigger('click');
                            $elemento.closest('tr').fadeOut(300, function () {
                                $(this).remove();
                            });
                            this.cuadroView.obtenerDetallesFilas();
                            Util.notify(data.tipo, data.mensaje);
                        }
                        else {
                            Util.notify(data.tipo, data.mensaje);
                        }
                    }).fail(() => {
                        $elemento.prop('disabled', false);
                        Util.notify('error', 'Hubo un problema al eliminar la fila. Por favor actualice la página e intente de nuevo');
                    });
                }
            })

        });
    }

    nuevoProveedorEvent() {
        $('#aNuevoProveedor').on('click', (e) => {
            e.preventDefault();
            $('#modalNuevoProveedor').modal('show');
        });

        $('#btnRegistrarProveedor').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Registrando');
            this.modelProveedor.registrar($('#formNuevoProveedor').serialize()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
                if (data.tipo == 'success') {
                    const $selectProveedor = $('#selectProveedor');
                    $selectProveedor.append('<option value="' + data.proveedor.id + '">' + data.proveedor.razon_social + '</option>');
                    $selectProveedor.selectpicker('refresh');
                    $selectProveedor.selectpicker('val', data.proveedor.id);
                    $('#modalNuevoProveedor').modal('hide');
                }

            }).fail(() => {
                Util.notify('error', 'Hubo un problema al registrar al proveedor. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Registrar');
            });
        })

        $('#formNuevoProveedor').on('keyup', 'input', (e) => {
            if (e.key == 'Enter') {
                $('#btnRegistrarProveedor').trigger('click');
            }
        })
    }

    seleccionarMejorPrecioEvent() {
        $('#btnSeleccionarMejorPrecio').on('click', (e) => {
            const $boton = $(e.currentTarget);
            $boton.html(Util.generarPuntosSvg() + 'Seleccionando');
            $boton.prop('disabled', true);
            $('#tableProveedoresFila tbody').find('button').prop('disabled', true);
            this.cuadroView.model.seleccionarMejorPrecio($('#txtProveedorIdFila').val()).then((resultado) => {
                switch (resultado.tipo) {
                    case 'success':
                        $('#tableProveedoresFila tbody').find('button.seleccionar').each(function () {
                            $(this).closest('tr').find('td.seleccionado').html('');
                            if ($(this).data('id') == resultado.id) {
                                $(this).closest('tr').find('td.seleccionado').html('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
                            }
                        });
                        this.cuadroView.obtenerDetallesFilas();
                        break;
                    case 'error':
                        Swal.fire({
                            icon: resultado.tipo,
                            title: resultado.titulo,
                            text: resultado.texto
                        })
                        break;
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al seleccionar el precio. Por favor actualice la página e intente de nuevo');
            }).always(() => {
                $('#tableProveedoresFila tbody').find('button').prop('disabled', false);
                $boton.prop('disabled', false);
                $boton.html('Seleccionar mejor precio');
            });
        });
    }

    actualizarCampoEvent() {
        let contenedor = "";
        $('#tableProveedoresFila tbody').on('focus', 'td.success', (e) => {
            contenedor = $(e.currentTarget).html();
        });

        $('#tableProveedoresFila tbody').on('blur', 'td.success, td.danger', (e) => {
            const $elemento = $(e.currentTarget);
            if (contenedor != $elemento.html()) {
                $elemento.removeClass('success').removeClass('danger').addClass('warning');
                this.cuadroView.model.actualizarCampoProveedor($elemento.data('id'), $elemento.data('campo'), $elemento.html()).then((data) => {
                    if (data.tipo == 'success') {
                        $elemento.removeClass('warning').addClass('success');
                        this.cuadroView.obtenerDetallesFilas();
                    }
                    else {
                        $elemento.html($elemento.html() + 'X');
                        $elemento.removeClass('warning').addClass('danger');
                        Util.notify(data.tipo, data.mensaje);
                    }
                }).fail(() => {
                    $elemento.html($elemento.html() + 'X');
                    $elemento.removeClass('warning').addClass('danger');
                    Util.notify('error', 'Hubo un problema al actualizar el valor. Por favor actualice la página e intente de nuevo');
                });
            }
        });
    }

    historialPreciosEvent = () => {
        $('#tableProveedoresFila').on("click", "button.historial-precios", (e) => {
            const $elemento = $(e.currentTarget);
            $elemento.prop('disabled', true);
            const moneda = $elemento.closest('tr').find('td:eq(2)').html();
            //var idFila = $elemento.data('id');
            $('#tableProveedoresFila').find(`tr.${$elemento.data('id')}`).find('button.cerrar-historial').trigger('click');
            this.cuadroView.model.obtenerHistorialPrecios($elemento.data('id')).then((data) => {
                let filas = `<tr class="${$elemento.data('id')}"><td class="text-center" colspan="9" style="font-size:16px;margin-top: 6px;"><strong>Historial de precios</strong></td></tr>`;
                for (let indice in data) {
                    filas += `<tr class="${$elemento.data('id')}">
                    <td></td>
                    <td colspan="3" class="text-center">${data[indice].user.name}</td>
                    <td class="text-right">${moneda}${Util.formatoNumero(data[indice].precio, 2)}</td>
                    <td class="text-center" colspan="2">${data[indice].fecha_format}</td>
                    <td></td>
                    <td></td>
                   </tr>`;
                }
                filas += `<tr class="${$elemento.data('id')}">
                <td colspan="9" class="text-center"><button class="btn btn-sm btn-default cerrar-historial" data-id="${$elemento.data('id')}">Cerrar historial</button></td>
                </tr>`;
                $(filas).insertAfter($elemento.closest('tr'));
            }).always(() => {
                $elemento.prop('disabled', false);
            });
        });

        $('#tableProveedoresFila').on("click", "button.cerrar-historial", function (e) {
            $('#tableProveedoresFila').find('tr.' + $(e.currentTarget).data('id')).fadeOut(300, function () {
                $(this).remove();
            });
        });
    }
}
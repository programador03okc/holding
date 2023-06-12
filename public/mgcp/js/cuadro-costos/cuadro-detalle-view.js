class CuadroDetalleView extends CuadroBaseView {
    constructor(id, model, tabla) {
        super(id, model);
        this.tabla = tabla;
    }

    actualizarCampoFilaEvent = () => {
        let contenedor = "";

        $(this.tabla).find('tbody').on("focus", "td.success, td.danger", (e) => {
            contenedor = $(e.currentTarget).html();
        });

        $(this.tabla).find('tbody').on("blur", "td.success:not(.numero-parte), td.danger:not(.numero-parte)", (e) => {
            if (contenedor != $(e.currentTarget).html()) {
                this.actualizarCampoFila($(e.currentTarget));
            }
        });

        $(this.tabla).find('tbody').on("change", "input, select", (e) => {
            this.actualizarCampoFila($(e.currentTarget));
        });
    }

    actualizarCampoFila = ($elemento) => {
        let valor = '';
        let $celda;
        if ($elemento.is('td')) {
            valor = $elemento.html();
            $celda = $elemento;
            if ($celda.hasClass('decimal')) {
                $celda.html(valor == '' ? '' : Util.formatoNumero(valor.split(',').join(''), 2, '.', ','));
            }
            if ($celda.hasClass('decimal0')) {
                var nvalor = valor.split('.');
                if (nvalor.length > 1) {
                    var decimal0 = (nvalor[1]).length;
                    if (decimal0 <= 2) {
                        $celda.html(valor == '' ? '' : Util.formatoNumero(valor.split(',').join(''), 2, '.', ','));
                    } else {
                        $celda.html(valor == '' ? '' : Util.formatoNumero(valor.split(',').join(''), decimal0, '.', ','));
                    }
                } else {
                    $celda.html(valor == '' ? '' : Util.formatoNumero(valor.split(',').join(''), 2, '.', ','));
                }
            }
        }
        else {
            $celda = $elemento.closest('td');
        }

        if ($elemento.is('select') || $elemento.is('input:text')) {
            valor = $elemento.val();
        }

        if ($elemento.is(':checkbox')) {
            valor = $elemento.is(':checked') ? 1 : 0;
        }

        $celda.removeClass('success').removeClass('danger').addClass('warning');
        this.model.actualizarCampoFila($elemento.data('id'), $elemento.data('campo'), valor).then((data) => {
            if (data.tipo == 'success') {
                this.obtenerDetallesFilas();
                $celda.removeClass('warning').addClass('success');
            }
            else {
                //Si existe la propiedad valor significa que el sistema está devolviendo el valor inicial de la fila
                if (data.valor != null) {
                    $celda.removeClass('warning').addClass('success');
                    $elemento.html(data.valor);
                }
                if (data.tipo == 'danger') {
                    alert(data.mensaje);
                }
                else {
                    $celda.removeClass('warning').addClass('danger');
                    Util.notify(data.tipo, data.mensaje);
                }
            }
        }).fail(() => {
            if ($elemento.is('td')) {
                $elemento.html($elemento.html() + 'X');
            }
            if ($elemento.is('input:text')) {
                $elemento.val($elemento.val() + 'X');
            }
            $celda.removeClass('warning').addClass('danger');
            Util.notify('error', 'Hubo un problema al actualizar el campo. Por favor actualice la página e intente de nuevo');
        });
    }

    eliminarFilaEvent = () => {
        $(this.tabla + ' tbody').on("click", "button.eliminar", (e) => {
            eliminarFila($(e.currentTarget));
        });

        //Eliminar fila con ESC
        $(this.tabla + ' tbody').on("keyup", "td.escape", (e) => {
            if (e.which == 27) {
                eliminarFila($(e.currentTarget).closest('tr').find('button.eliminar'));
            }
        });

        const eliminarFila = ($boton) => {
            Swal.fire({
                icon: 'question',
                title: `¿Está seguro de eliminar la fila?`,
                confirmButtonText: 'Sí',
                showDenyButton: true,
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $boton.prop('disabled', true);
                    this.model.eliminarFila($boton.data('id')).then((data) => {
                        Util.notify(data.tipo, data.mensaje);
                        if (data.tipo == 'success') {
                            const objeto = this;
                            $boton.closest('tr').fadeOut(300, function () {
                                $(this).remove();
                                objeto.obtenerDetallesFilas();
                            });
                        }
                    }).fail(() => {
                        $boton.prop('disabled', false);
                        Util.notify('error', 'Hubo un problema al eliminar la fila. Actualice la página e intente de nuevo');
                    });
                }
            })
        }
    }
}
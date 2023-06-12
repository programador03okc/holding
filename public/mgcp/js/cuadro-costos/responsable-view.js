class ResponsableView {
    constructor(idCuadro, model) {
        this.idCuadro = idCuadro;
        this.model = model;
        this.agregarEvent();
        this.eliminarEvent();
        this.calcularPorcentajeEvent();
        this.actualizarEvent();
    }

    agregarEvent = () => {
        $('#btnAgregarResponsable').on('click', (e) => {
            const $boton = $(e.currentTarget);
            const $corporativos = $('#selectCorporativos');
            $boton.prop('disabled', true);
            this.model.agregar(this.idCuadro).then((data) => {
                let fila = `<tr>
                <td>
                    <select data-id="${data.id}" class="form-control input-sm corporativo responsable">${$corporativos.html()}</select>
                </td>
                <td><input value="0" data-id="${data.id}" type="text" class="form-control responsable porcentaje input-sm text-right"></td>
                <td class="text-center">
                    <button data-id="${data.id}" title="Retirar responsable" class="eliminar btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></button>
                </td>
                </tr>`;
                $('#tableResponsables').find('tbody').append(fila);
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al agregar el responsable. Actualice la página e inténtelo de nuevo');
            }).always(() => {
                $boton.prop('disabled', false);
            });
        });
    }

    actualizarEvent = () => {
        $('#modalResponsables').on('change', '.responsable', (e) => {
            const $elemento = $(e.currentTarget);
            const $fila = $elemento.closest('tr');
            this.model.actualizar($elemento.data('id'), $fila.find('select.responsable').val(), $fila.find('input.responsable').val()).then((data) => {
                Util.notify(data.tipo, data.mensaje);
            }).fail(() => {
                Util.notify("error", "Hubo un problema al actualizar el responsable. Actualice la página e inténtelo de nuevo");
            });
        });
    }

    eliminarEvent = () => {
        $('#modalResponsables').on('click', 'button.eliminar', (e) => {
            const obj = this;
            const $boton = $(e.currentTarget);
            Swal.fire({
                icon: 'question',
                title: `¿Está seguro de retirar al responsable?`,
                confirmButtonText: 'Sí',
                showDenyButton: true,
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $boton.prop('disabled', true);
                    this.model.eliminar($boton.data('id')).then((data) => {
                        
                        if (data.tipo == 'success') {
                            $boton.closest('tr').fadeOut(300, function () {
                                $(this).remove();
                                obj.calcularPorcentaje();
                            });
                        }
                        else {
                            $boton.prop('disabled', true);
                            Util.notify(data.tipo, data.mensaje);
                        }
                    }).fail(() => {
                        $boton.prop('disabled', true);
                        Util.notify('error', 'Hubo un problema al agregar el responsable. Actualice la página e inténtelo de nuevo');

                    });
                }
            })
        });
    }

    calcularPorcentajeEvent = () => {
        $('#modalResponsables').on('keyup', 'input.porcentaje', ()=> {
            this.calcularPorcentaje();
        });
        this.calcularPorcentaje();
    }

    calcularPorcentaje = () => {
        let porcentaje = 0;
        $('#modalResponsables').find('input.porcentaje').each(function () {
            porcentaje += parseInt($(this).val());
        });
        let mostrar = '';
        if (porcentaje != 100) {
            mostrar = '<span class="text-danger">' + porcentaje + '%</span>'
        } else {
            mostrar = porcentaje + '%';
        }
        $('#strongTotalPorcentaje').html(mostrar);
    }
}
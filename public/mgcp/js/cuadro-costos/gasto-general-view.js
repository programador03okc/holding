class GastoGeneralView extends CuadroDetalleView
{
    constructor(id, model) {
        super(id, model, '#tableCcGg');
        this.agregarFilaEvent();
        this.actualizarCampoFilaEvent();
        this.actualizarCampoEvent();
        this.eliminarFilaEvent();
    }

    agregarFilaEvent = () => {
        $('#btnCcGgFila').on('click', (e) => {
            const $elemento = $(e.currentTarget);
            $elemento.prop('disabled', true);
            this.model.agregarFila($elemento.data('id')).then((data) => {
                const $tbody = $(this.tabla).find('tbody');
                $tbody.append(`
                <tr>
                    <td data-id="${data.id}" data-campo="descripcion" class="success descripcion" spellcheck="false" contenteditable="true"></td>
                    <td class="success"><select data-id="${data.id}" data-campo="id_categoria_gasto" style="font-size: x-small;" class="form-control input-sm categoria-gasto">';
                    ${$('#selectCategoriasGasto').html()}
                    </select>
                    </td>
                    <td data-id="${data.id}" data-campo="unidad" class="success text-center" contenteditable="true">UND</td>';
                    <td data-id="${data.id}" data-campo="personas" class="success text-center entero personas" contenteditable="true"></td>';
                    <td data-id="${data.id}" data-campo="porcentaje_participacion" class="success text-center entero participacion" contenteditable="true"></td>';
                    <td data-id="${data.id}" data-campo="tiempo" class="success text-center decimal tiempo" contenteditable="true"></td>';
                    <td data-id="${data.id}" data-campo="costo" class="success text-right tab decimal costo" contenteditable="true"></td>';
                    <td class="text-right subtotal"></td>';
                    <td class="text-right otros"></td>';
                    <td class="text-right total"></td>';
                    <td class="text-center">
                    <button title="Eliminar" data-id="${data.id}" class="btn btn-xs eliminar"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                    </td>
                </tr>`
                );
                $tbody.find(`tr: eq(${($tbody.find('tr').length - 1)}) td: eq(0)`).trigger('focus');
            }).always(() => {
                $elemento.prop('disabled', false);
            });
        });
    }

    actualizarCampoEvent = () => {

        /*$('#txtFechaCcVenta').on('change', (e) => {
            this.actualizarCampo($(e.currentTarget));
            $('#spanCcVentaFecha').html($(e.currentTarget).val());
        });

        $('#txtCcVentaMargen').on('change', (e) => {
            this.actualizarCampo($(e.currentTarget));
            $('#spanCcVentaMargen').html(Util.formatoNumero($(e.currentTarget).val().split(',').join(''), 2, '.', ','));
        });*/
    }
}
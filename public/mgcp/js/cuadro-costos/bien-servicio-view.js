class BienServicioView extends CuadroProductoView {
    constructor(id, model, proveedorView, comentarioView) {
        super(id, model, '#tableCcBs', proveedorView, comentarioView);
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
        $('#btnCcBsFila').on('click', (e) => {
            const $elemento = $(e.currentTarget);
            $elemento.prop('disabled', true);
            this.model.agregarFila($elemento.data('id')).then((data) => {
                const $tbody = $(this.tabla).find('tbody');
                $tbody.append(`
                <tr>
                    <td data-id="${data.id}" class="success numero-parte escape" contenteditable="true" spellcheck="false"></td>
                    <td data-id="${data.id}" data-campo="descripcion" class="success descripcion" contenteditable="true" spellcheck="false"></td>
                    <td data-id="${data.id}" data-campo="unidad" class="text-center success" contenteditable="true" spellcheck="false">UND</td>
                    <td data-id="${data.id}" data-campo="cantidad" class="text-center success decimal cantidad tab" contenteditable="true"></td>
                    <td data-id="${data.id}" class="proveedor-nombre info"></td>
                    <td data-id="${data.id}" class="proveedor-precio info text-right"></td>
                    <td data-id="${data.id}" class="proveedor-plazo info text-center"></td>
                    <td data-id="${data.id}" class="proveedor-flete info text-right"></td>
                    <td data-id="${data.id}" class="proveedor-fondo info text-center"></td>
                    <td class="text-right costo-total"></span></td>
                    <td class="text-right costo-total-convertido"></td>
                    <td class="text-right flete-total"></td>
                    <td class="text-right costo-flete-total"></td>
                    <td class="text-center">
                    <button data-id="${data.id}" class="btn btn-xs eliminar"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>
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
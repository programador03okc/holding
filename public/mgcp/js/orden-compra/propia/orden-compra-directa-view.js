class OrdenCompraDirectaView {
    /*constructor(token) {
        this.token = token;
    }*/
    nuevaEvent() {

        $('#formRegistrarOc').on('submit',(e)=> {
            if ($('#selectEntidad').val() == null) {
                e.preventDefault();
                alert('Seleccione una entidad antes de continuar');
                return;
            }
            
            let $boton = $('#btnRegistrarOc');
            $boton.html(Util.generarPuntosSvg() + 'Registrando');
            $boton.prop('disabled', true);
        });
    }

    cambiarMonedaEvent()
    {
        $('#divMonto').on('click','a.moneda',(e)=>{
            e.preventDefault();
            $('#spanMoneda').html($(e.currentTarget).html());
            $('#txtMoneda').val($(e.currentTarget).data('moneda'));
            this.mostrarTipoCambio($(e.currentTarget).data('moneda'));
        })
    }

    mostrarTipoCambio(moneda)
    {
        if (moneda=='s')
        {
            $('#lblTipoCambio').addClass('hidden');
            $('#divTipoCambio').addClass('hidden');
        }
        else
        {
            $('#lblTipoCambio').removeClass('hidden');
            $('#divTipoCambio').removeClass('hidden');
        }
    }
}
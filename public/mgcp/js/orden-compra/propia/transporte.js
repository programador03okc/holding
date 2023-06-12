var Transporte = (function () {

    var token;

    function init(_token)
    {
        token = _token;
    }

    function listar($elemento, ruta)
    {
        var $modal = $('#modalTransportes');
        var $tbody = $('#tbodyTransportes');
        var $field = $('#fieldsetTransportes');
        $modal.find('h4.modal-title').html('Transportes para ' + $elemento.data('orden'));
        $tbody.html('<tr><td colspan="3" class="text-center">Obteniendo datos...</td></tr>');
        $field.hide();
        $modal.modal('show');
        $.ajax({
            url: ruta,
            type: 'post',
            data: {idOc: $elemento.data('id'), _token: token},
            success: function (datos) {
                var cadena = '';
                for (var indice in datos) {
                    cadena += '<tr>';
                    cadena += '<td class="text-center">' + datos[indice].fecha + '</td>';
                    cadena += '<td class="text-center">' + datos[indice].transportista.empresa + '</td>';
                    cadena += '<td class="text-justify">' + datos[indice].nro_guia + '</td>';
                    cadena += '<td class="text-center"><button title="Eliminar" data-id="' + datos[indice].id + '" class="btn btn-default btn-sm eliminar"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>';
                    cadena += '</tr>';
                }
                $tbody.html(cadena);
                $('#btnNuevoTransporte').data('id', $elemento.data('id'));
                $field.show();
            },
            error: function () {
                alert("Error al obtener los datos. Actualice la página y vuelva a intentarlo.");
            }
        });
    }

    function eliminar($elemento, ruta)
    {
        $.ajax({
            url: ruta,
            type: 'post',
            data: {id: $elemento.data('id'), _token: token},
            success: function () {
                $elemento.closest('tr').fadeOut(300, function () {
                    $(this).remove();
                });
            },
            error: function () {
                alert("Error al eliminar los datos. Actualice la página y vuelva a intentarlo.");
            }
        });
    }



    function registrar(ruta)
    {
        var $elemento=$("#btnNuevoTransporte");
        $elemento.attr('disabled', true);
        var $transportista = $('#divTransportistas').find('select');
        var fecha = $('#txtTransporteFecha').val();
        var $nroGuia = $('#txtTransporteNroGuia');
        $.ajax({
            url: ruta,
            type: 'post',
            data: {idOc: $elemento.data('id'), idTransportista: $transportista.val(),
                fecha: fecha, nroGuia: $nroGuia.val(), _token: token},
            success: function (dato) {
                var cadena = '<tr>';
                cadena += '<td class="text-center">' + fecha + '</td>';
                cadena += '<td class="text-center">' + $transportista.find('option:selected').html() + '</td>';
                cadena += '<td class="text-justify">' + $nroGuia.val() + '</td>';
                cadena += '<td class="text-center"><button title="Eliminar" data-id="' + dato + '" class="btn btn-default btn-sm eliminar"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>';
                cadena += '</tr>';
                $('#tbodyTransportes').append(cadena);
                $nroGuia.val('');
            },
            error: function () {
                alert("Hubo un problema al registrar el transporte. Actualice la página y vuelva a intentarlo.");
            },
            complete: function () {
                $elemento.attr('disabled', false);
            }
        });
    }

    return {
        init: init,
        listar: listar,
        registrar: registrar,
        eliminar: eliminar
    };
})();



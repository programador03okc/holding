var Transportista = (function () {

    var token;

    function init(_token)
    {
        token = _token;
    }

    function registrar(rutaRegistrar, rutaObtener)
    {
        var empresa = prompt('Ingrese nueva empresa');
        if (empresa != null)
        {
            $.ajax({
                url: rutaRegistrar,
                type: 'post',
                data: {empresa: empresa, _token: token},
                success: function (dato) {
                    switch (dato)
                    {
                        case 'ok':
                            listar(empresa, rutaObtener);
                            break;
                        case 'existe':
                            alert('Error: La empresa ya ha sido registrada anteriormente');
                            break;
                        case 'vacio':
                            alert('Error: Debe ingresar un nombre de empresa.');
                            break;
                    }
                },
                error: function () {
                    alert('Hubo un error al registrar los datos. Por favor actualice la página e intente de nuevo.');
                }
            });
        }
    }


    function listar(empresa, ruta)
    {
        var $contenedor = $('#divTransportistas');
        $contenedor.html('Obteniendo datos...');
        $.ajax({
            url: ruta,
            type: 'post',
            data: {_token: token},
            success: function (datos) {
                var select = '<select class="form-control transportista">';
                for (var indice in datos) {
                    select += '<option value="' + datos[indice].id + '">' + datos[indice].empresa + '</option>';
                }
                select += '</select>';
                $contenedor.html(select);
                if (empresa !== '')
                {
                    $contenedor.find('option:contains(' + empresa + ')').attr('selected', 'selected');
                }
            },
            error: function () {
                $contenedor.html('<span class="text-danger">Error. Actualice la página.</span>');
            }
        });
    }

    return {
        init: init,
        listar: listar,
        registrar: registrar
    };
})();

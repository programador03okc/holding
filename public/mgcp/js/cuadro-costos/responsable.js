var Responsable = (function () {

    var idCuadro, token;

    function init(_idCuadro, _token)
    {
        idCuadro = _idCuadro;
        token = _token;
    }

    function agregar(ruta)
    {
        $('#btnAgregarResponsable').click(function () {
            var $boton = $(this);
            var $corporativos = $('#selectCorporativos');
            $boton.prop('disabled', true);

            $.ajax({
                url: ruta,
                type: 'post',
                dataType: 'json',
                data: {idCuadro: idCuadro, idCorporativo: $corporativos.val(), _token: token},
                success: function (data) {
                    var fila = '<tr>' +
                            '<td><select data-id="' + data.id + '" class="form-control input-sm corporativo responsable">' + $corporativos.html() + '</select></td>' +
                            '<td><input value="0" data-id="' + data.id + '" type="text" class="form-control responsable porcentaje input-sm text-right"></td>' +
                            '<td class="text-center"><button data-id="' + data.id + '" title="Retirar responsable" class="eliminar btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span></button></td>' +
                            '</tr>';
                    $('#tableResponsables').find('tbody').append(fila);
                },
                error: function () {
                    alert("Hubo un problema al agregar el responsable. Actualice la página e inténtelo de nuevo.");
                },
                complete: function () {
                    $boton.prop('disabled', false);
                }
            });
        });
    }

    function actualizar(ruta)
    {
        $('#modalResponsables').on('change', '.responsable', function () {
            var $elemento = $(this);
            var $fila = $(this).closest('tr');
            $.ajax({
                url: ruta,
                type: 'post',
                dataType: 'json',
                data: {id: $elemento.data('id'), idCorporativo: $fila.find('select.responsable').val(), porcentaje: $fila.find('input.responsable').val(), _token: token},
                success: function (data) {
                    if (data.tipo != 'success')
                    {
                        alert(data.mensaje);
                    }
                },
                error: function () {
                    alert("Hubo un problema al agregar el responsable. Actualice la página e inténtelo de nuevo.");
                }
            });
        });
    }

    function eliminar(ruta)
    {

        $('#modalResponsables').on('click', 'button.eliminar', function () {

            var $elemento = $(this);
            if (confirm("¿Está seguro de retirar al responsable?"))
            {
                $.ajax({
                    url: ruta,
                    type: 'post',
                    dataType: 'json',
                    data: {id: $elemento.data('id'), _token: token},
                    success: function (data) {
                        if (data.tipo == 'success')
                        {
                            $elemento.closest('tr').fadeOut(300, function () {
                                $(this).remove();
                                calcularPorcentaje();
                            });
                        }
                    },
                    error: function () {
                        alert("Hubo un problema al agregar el responsable. Actualice la página e inténtelo de nuevo.");
                    }
                });
            }
        });
    }

    function calcularPorcentaje()
    {
        $('#modalResponsables').on('keyup', 'input.porcentaje', function () {
            var porcentaje = 0;
            $('#modalResponsables').find('input.porcentaje').each(function () {
                porcentaje += parseInt($(this).val());
            });
            var mostrar = '';
            if (porcentaje != 100)
            {
                mostrar = '<span class="text-danger">' + porcentaje + '%</span>'
            } else
            {
                mostrar = porcentaje + '%';
            }
            $('#strongTotalPorcentaje').html(mostrar);
        });
        $('#txtPorcentajeResponsable').keyup();
    }

    return {
        init: init,
        agregar: agregar,
        actualizar: actualizar,
        eliminar: eliminar,
        calcularPorcentaje: calcularPorcentaje
    };
})();
function Gasto(token) {
    this.token = token;
}


Gasto.prototype.listar = function (rutaNueva) {
    $('#tableDatos').DataTable({
        dom: 'Bfrtip',
        pageLength: 20,
        language: {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        columnDefs: [
            {orderable: false, targets: [6]},
            {className: "text-right", targets: [2, 3, 4]},
            {className: "text-center", targets: [1, 5, 6]}
        ],
        buttons: [
            {
                text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo',
                action: function () {
                    location.href = rutaNueva;
                }
            }
        ]
    });
}

Gasto.prototype.eliminar = function (ruta) {
    var instancia = this;

    $('#tableDatos').on("click", "button.eliminar", function () {
        var $boton = $(this);
        if (confirm('¿Está seguro de eliminar el registro?'))
        {
            $boton.prop('disabled', true);
            $.ajax({
                url: ruta,
                type: 'post',
                dataType: 'json',
                data: {id: $boton.data('id'), _token: instancia.token},
                success: function (data) {
                    
                    if (data.tipo == 'success')
                    {
                        $('#tableDatos').DataTable().row($boton.closest('tr')).remove().draw();
                    }
                    else
                    {
                        alert(data.mensaje);
                    }
                },
                error: function () {
                    alert("Hubo un problema al intentar eliminar el registro. Por favor actualice la página e inténtelo de nuevo.")
                },
                complete: function ()
                {
                    $boton.prop('disabled', false);
                }
            });
        }
    });
}

Gasto.prototype.activar = function (idCuadro, ruta) {
    var instancia = this;

    $('#tableGastos').on("change", "input[type=checkbox]", function () {
        var $elemento = $(this);
        var fila;
        if ($elemento.is(':checked'))
        {
            var cantColumnas;
            var despuesDe;
            switch ($elemento.data('operacion'))
            {
                case 1:
                    fila = '<tr class="' + $elemento.data('id') + '"><td class="text-right" colspan=""><strong>' + $elemento.closest('tr').find('td:eq(0)').html() + ' (' + $elemento.data('porcentaje') + '%):</strong></td>';
                    fila += '<td class="text-right bordered gasto" data-afectacion="' + $elemento.data('afectacion') + '" data-porcentaje="' + $elemento.data('porcentaje') + '" data-desde="' + $elemento.data('desde') + '" data-hasta="' + $elemento.data('hasta') + '"></td></tr>';
                    $(fila).insertAfter($().closest('tr'));
                    cantColumnas = 16;
                    despuesDe = '#tdCcVentaGastosGenerales';
                    break;
                case 2:
                    cantColumnas = 15;
                    despuesDe = '#tdCcAmGastosGenerales';
                    break;
            }
            fila = '<tr class="' + $elemento.data('id') + '"><td class="text-right" colspan="' + cantColumnas + '"><strong>' + $elemento.closest('tr').find('td:eq(0)').html() + ' (' + $elemento.data('porcentaje') + '%):</strong></td>';
            fila += '<td class="text-right bordered gasto" data-afectacion="' + $elemento.data('afectacion') + '" data-porcentaje="' + $elemento.data('porcentaje') + '" data-desde="' + $elemento.data('desde') + '" data-hasta="' + $elemento.data('hasta') + '"></td></tr>';
            $(fila).insertAfter($(despuesDe).closest('tr'));
        } else
        {
            var tabla;
            switch ($elemento.data('operacion'))
            {
                case 1:
                    tabla = '#tableCcVenta';
                    break;
                case 2:
                    tabla = '#tableCcAm';
                    break;
            }
            $(tabla).find('tfoot').find('tr.' + $elemento.data('id')).remove();
        }

        switch ($elemento.data('operacion'))
        {
            case 1:
                CcVenta.calcularMontos();
                break;
            case 2:
                CcAcuerdoMarco.calcularMontos();
                break;
        }

        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: {id: $elemento.data('id'), idCuadro: idCuadro, activar: $elemento.is(':checked'), _token: instancia.token},
            error: function () {
                alert("Hubo un problema al gestionar el gasto. Por favor actualice la página e inténtelo de nuevo.")
            }
        });
    });
}
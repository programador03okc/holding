class ComentarioView {
    constructor(model) {
        this.model = model;
        this.registrarEvent();
    }

    obtenerLista = ($elemento) => {
        $('#modalComentarios').modal('show');
        const $botonRegistrar=$('#btnRegistrarComentario');
        $botonRegistrar.data('id',$elemento.data('id'));
        $botonRegistrar.data('tabla',$elemento.closest('table').attr('id'));
        const $tbody = $('#tbodyComentarios');
        $tbody.html('<tr><td colspan="3" class="text-center">Obteniendo comentarios...</td></tr>');
        this.model.listarComentarios($elemento.data('id')).then((data) => {
            if (data.length > 0) {
                let filas = '';
                for (let indice in data) {
                    filas += `<tr>
                    <td>${data[indice].usuario.name}</td>
                    <td class="text-center">${data[indice].fecha}</td>
                    <td>${data[indice].comentario}</td>
                    </tr>`;
                }
                $tbody.html(filas);
            }
            else {
                $tbody.html('<tr id="trSinComentarios"><td colspan="3" class="text-center">No hay comentarios registrados</td></tr>');
            }
        });
    }

    registrarEvent = () => {

        $('#btnRegistrarComentario').on('click', (e) => {
            
            const $boton = $(e.currentTarget);
            $boton.prop('disabled', true);
            $boton.html(Util.generarPuntosSvg()+'Registrando');
            this.model.registrarComentario($boton.data('id'), $('#txtFilaComentario').val()).then((data) => {
                if (data.tipo == 'success') {
                    $('#trSinComentarios').remove();
                    $('#tbodyComentarios').append(`
                    <tr>
                        <td>${data.autor}</td>
                        <td class="text-center">${data.fecha}</td>
                        <td>${data.comentario}</td>
                    </tr>`);
                    $('#txtFilaComentario').val('');
                    $('#'+$boton.data('tabla')).find('button.comentarios[data-id=' + $boton.data('id') + ']').removeClass('btn-default').addClass('btn-info');
                }
                else {
                    Util.notify(data.tipo, data.mensaje);
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al registrar el comentario. Por favor vuelva a intentarlo');
            }).always(() => {
                $boton.html('Registrar');
                $boton.prop('disabled', false);
            });
        });

        /*var $boton = $('#btnEnviarComentario');
        $boton.prop('disabled', true);
        $.ajax({
            url: ruta,
            type: 'post',
            dataType: 'json',
            data: { idFila: $boton.data('fila'), comentario: $('#txtFilaComentario').val(), _token: token },
            success: function (data) {
                if (data.tipo == 'success') {
                    var fila = '<tr>';
                    fila += '<td>' + data.autor + '</td>';
                    fila += '<td class="text-center">' + data.fecha + '</td>';
                    fila += '<td>' + $('#txtFilaComentario').val() + '</td>';
                    fila += '</tr>';
                    $('#tbodyComentarios').append(fila);
                    $('#txtFilaComentario').val('');

                    var idTabla = $boton.data('tipo') == "am" ? '#tableCcAm' : '#tableCcVenta'
                    $(idTabla).find('button.comentarios[data-id=' + $boton.data('fila') + ']').removeClass('btn-default').addClass('btn-info');
                }
            },
            error: function () {
                alert("Error: No se pudieron ingresar los datos. Actualice la página e inténtelo de nuevo.");
            },
            complete: function () {
                $boton.prop('disabled', false);
            }
        });*/
    }
}
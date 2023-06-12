class ComentarioOcView {
    constructor(model) {
        this.model = model;
        this.boton=null;
    }

    listarComentariosEvent() {
        $('tbody').on('click', '.ver-comentarios-oc', (e) => {
            const $elemento = $(e.currentTarget);
            this.boton=$elemento;
            const $modal = $('#modalComentarios');
            const $tbody = $('#tbodyComentarios');
            const $botonEnviar = $('#btnRegistrarComentario');
            $botonEnviar.attr('disabled', true);
            $modal.find('h4.modal-title').html('Comentarios para ' + $elemento.data('orden'));
            Util.bloquearConSpinner($modal.find('div.mensaje'));
            $tbody.html('');
            $modal.modal('show');
            this.model.listarPorOc($elemento.data('id'), $elemento.data('tipo')).then((datos) => {
                if (datos.tipo == 'success') {
                    let cadena = '';
                    if (datos.comentarios.length == 0) {
                        cadena = '<tr class="sin-comentarios"><td class="text-center" colspan="3">Sin comentarios registrados</td></tr>';
                    }
                    else {
                        for (let indice in datos.comentarios) {
                            cadena += `<tr>
                                <td>${datos.comentarios[indice].usuario.name}</td>
                                <td class="text-justify">${datos.comentarios[indice].comentario}</td>
                                <td class="text-center">${datos.comentarios[indice].fecha}</td>
                            </tr>`;
                        }
                    }
                    $tbody.html(cadena);
                    $botonEnviar.data('id', $elemento.data('id'));
                    $botonEnviar.data('tipo', $elemento.data('tipo'));
                    $botonEnviar.attr('disabled', false);
                }
                else {
                    alert(data.mensaje);
                }

            }).fail(() => {
                alert('Hubo un problema al obtener los comentarios. Por favor actualice la página e intente de nuevo');
                $modal.modal('hide');
            }).always(() => {
                Util.liberarBloqueoSpinner($modal.find('div.mensaje'));
            });
        });
    }

    registrarComentarioEvent() {

        $('#btnRegistrarComentario').on('click',(e) => {
            const $boton = $(e.currentTarget);
            const $textarea = $('#modalComentarios').find('textarea');
            const $tbody = $('#tbodyComentarios');
            if ($textarea.val() == '') {
                alert("Ingrese un comentario antes de continuar.");
                $textarea.trigger('focus');
                return;
            }
            $boton.attr('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Registrando');
            this.model.registrarComentario($boton.data('id'), $boton.data('tipo'),$textarea.val()).then((datos) => {
                $tbody.find('tr.sin-comentarios').remove();
                let fila = `
                <tr>
                    <td>${datos.usuario}</td>
                    <td class="text-justify">${$textarea.val()}</td>
                    <td class="text-center">${datos.fecha}</td>
                </tr>`;
                $tbody.append(fila);
                $textarea.val('');
                this.boton.removeClass('btn-default').removeClass('btn-info').addClass('btn-info')
                //$('#tableOrdenes').DataTable().ajax.reload();
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al registrar el comentario. Por favor vuelva a intentarlo');
            }).always(() => {
                $boton.attr('disabled', false);
                $boton.html('Registrar');
            });
        });
    }
}
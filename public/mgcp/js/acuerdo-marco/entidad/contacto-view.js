class ContactoEntidadView {
    constructor(model, puedeGestionarContactos, seleccionarContacto) {
        this.model = model;
        this.puedeGestionarContactos = puedeGestionarContactos;
        this.seleccionarContacto = seleccionarContacto;
        this.listarEvent();
        this.agregarEvent();
        this.eliminarEvent();
        this.editarEvent();
    }

    agregarEvent = () => {

        $('#modalAgregarContactoEntidad').on('shown.bs.modal', () => {
            $('#modalAgregarContactoEntidad').find('input[name=nombre]').trigger('focus');
        });

        $('#modalAgregarContactoEntidad input').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnRegistrarContactoEntidad').trigger('click');
            }
        });

        $('#btnAgregarContactoEntidad').on('click', (e) => {
            const $modal = $('#modalAgregarContactoEntidad');
            $modal.find('input[type=text]').val('');
            $modal.modal('show');
        });

        $('#btnRegistrarContactoEntidad').on('click', (e) => {
            const $form = $('#modalAgregarContactoEntidad').find('form');
            const $botonAgregar=$('#btnAgregarContactoEntidad');
            if (Util.validarCampos($form)) {
                const $boton = $(e.currentTarget);
                $boton.html(Util.generarPuntosSvg() + 'Agregando')
                $boton.prop('disabled', true);
                this.model.agregar($form.serialize()).then((data) => {
                    Util.notify(data.tipo, data.mensaje);
                    if (data.tipo == 'success') {
                        $('#modalAgregarContactoEntidad').modal('hide');
                        $('#trSinContactosEntidad').remove();
                        $('#tableContactosEntidad tbody').append(`<tr>
                            <td>${data.contacto.nombre}</td>
                            <td class="text-center">${data.contacto.telefono}</td>
                            <td>${data.contacto.cargo}</td>
                            <td>${data.contacto.email}</td>
                            <td>${data.contacto.direccion}</td>
                            <td>${data.contacto.horario}</td>
                            <td class="text-center seleccionado">
                            <td class="text-center">
                                <button data-orden="${$botonAgregar.data('orden')}" data-tipo="${$botonAgregar.data('tipo')}" data-id="${data.contacto.id}" title="Seleccionar contacto" class="btn btn-success btn-xs seleccionar"><span class="glyphicon glyphicon-ok"></span></button><button data-id="${data.contacto.id}" title="Editar" class="btn btn-primary btn-xs editar"><span class="glyphicon glyphicon-pencil"></span></button><br>
                                <button data-id="${data.contacto.id}" title="Eliminar" class="btn btn-danger btn-xs eliminar"><span class="glyphicon glyphicon-remove"></span></button>
                                <button style="visibility: hidden;" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button>
                            </td>
                        </tr>`)
                        //this.listar($form.find('input[name=idEntidad]').val());
                    }
                }).fail(() => {
                    alert("Hubo un problema al agregar el contacto. Por favor actualice la página e intente de nuevo");
                }).always(() => {
                    $boton.prop('disabled', false);
                    $boton.html('Agregar');
                });
            }
        });
    }

    eliminarEvent = () => {
        $('#tableContactosEntidad').on('click', 'button.eliminar', (e) => {
            if (confirm("¿Desea eliminar este contacto?")) {
                const $boton = $(e.currentTarget);
                $boton.closest('td').find('button').prop('disabled', true);
                this.model.eliminar($boton.data('id')).then((data) => {
                    Util.notify(data.tipo, data.mensaje);
                    if (data.tipo == 'success') {
                        $boton.closest('tr').fadeOut(300, function () {
                            this.remove();
                        });
                    }
                }).fail(() => {
                    alert("Hubo un problema al eliminar el contacto. Por favor actualice la página e intente de nuevo");
                }).always(() => {
                    $boton.closest('td').find('button').prop('disabled', false);
                })
            }
        });
    }

    editarEvent = () => {
        $('#modalEditarContactoEntidad').on('shown.bs.modal', () => {
            $('#modalEditarContactoEntidad').find('input[name=nombre]').trigger('focus');
        });

        $('#modalEditarContactoEntidad input').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnActualizarContactoEntidad').trigger('click');
            }
        });

        $('#tableContactosEntidad').on('click', 'button.editar', (e) => {
            const $modal = $('#modalEditarContactoEntidad');
            $modal.find('input[type=text]').val('');
            $('#btnActualizarContactoEntidad').prop('disabled', true);
            $modal.modal('show');
            Util.bloquearConSpinner($modal.find('div.mensaje'));
            this.model.obtenerDetalles($(e.currentTarget).data('id')).then((data) => {
                $modal.find('input[name=idContacto]').val(data.contacto.id);
                $modal.find('input[name=idEntidad]').val(data.contacto.id_entidad);
                $modal.find('input[name=nombre]').val(data.contacto.nombre);
                $modal.find('input[name=telefono]').val(data.contacto.telefono);
                $modal.find('input[name=cargo]').val(data.contacto.cargo);
                $modal.find('input[name=correo]').val(data.contacto.email);
                $modal.find('input[name=direccion]').val(data.contacto.direccion);
                $modal.find('input[name=horario]').val(data.contacto.horario);
            }).fail(() => {
                alert("Hubo un problema al obtener los detalles del contacto. Por favor actualice la página e intente de nuevo");
            }).always(() => {
                $('#btnActualizarContactoEntidad').prop('disabled', false);
                Util.liberarBloqueoSpinner($modal.find('div.mensaje'));
            })
        });

        $('#btnActualizarContactoEntidad').on('click', (e) => {
            const $form = $('#modalEditarContactoEntidad').find('form');
            if (Util.validarCampos($form)) {
                const $boton = $(e.currentTarget);
                $boton.html(Util.generarPuntosSvg() + 'Actualizando')
                $boton.prop('disabled', true);
                this.model.actualizar($form.serialize()).then((data) => {
                    Util.notify(data.tipo, data.mensaje);
                    if (data.tipo == 'success') {
                        $('#modalEditarContactoEntidad').modal('hide');
                        this.listar($form.find('input[name=idEntidad]').val());
                    }
                }).fail(() => {
                    alert("Hubo un problema al actualizar el contacto. Por favor actualice la página e intente de nuevo");
                }).always(() => {
                    $boton.prop('disabled', false);
                    $boton.html('Actualizar');
                });
            }
        });
    }

    listar = (idEntidad) => {
        const $tbody = $('#tableContactosEntidad').find('tbody');
        const $botonAgregar=$('#btnAgregarContactoEntidad');
        $tbody.html(`<tr><td colspan="${this.seleccionarContacto ? '8' : 7}" class="text-center">Obteniendo contactos...</td></tr>`);
        this.model.listar(idEntidad, $botonAgregar.data('orden'),$botonAgregar.data('tipo')).then((data) => {
            let filas = '';
            if (data.contactos.length == 0) {
                filas = `<tr id="trSinContactosEntidad"><td colspan="${this.seleccionarContacto ? '8' : 7}" class="text-center">Sin contactos</td></tr>`;
            }
            else {
                for (let indice in data.contactos) {
                    filas += `<tr>
                <td>${data.contactos[indice].nombre}</td>
                <td class="text-center">${data.contactos[indice].telefono}</td>
                <td>${data.contactos[indice].cargo}</td>
                <td>${data.contactos[indice].email}</td>
                <td>${data.contactos[indice].direccion}</td>
                <td>${data.contactos[indice].horario}</td>`;
                    if (this.seleccionarContacto) {
                        filas += `<td class="text-center seleccionado">${data.seleccionado == data.contactos[indice].id ? '<span class="glyphicon glyphicon-ok"></span>' : ''}</td>`;
                    }
                    filas += `<td class="text-center">`;
                    if (this.puedeGestionarContactos == '1' && this.seleccionarContacto) {
                        filas += `<button data-orden="${$botonAgregar.data('orden')}" data-tipo="${$botonAgregar.data('tipo')}" data-id="${data.contactos[indice].id}" title="Seleccionar contacto" class="btn btn-success btn-xs seleccionar"><span class="glyphicon glyphicon-ok"></span></button>`;
                    }
                    if (this.puedeGestionarContactos == '1') {
                        filas += `<button data-id="${data.contactos[indice].id}" title="Editar" class="btn btn-primary btn-xs editar"><span class="glyphicon glyphicon-pencil"></span></button><br> 
                        <button data-id="${data.contactos[indice].id}" title="Eliminar" class="btn btn-danger btn-xs eliminar"><span class="glyphicon glyphicon-remove"></span></button>
                        <button style="visibility: hidden" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></button>`;
                    }
                    filas += `</td>
                </tr>`;
                }
            }
            $tbody.html(filas);
        });
    }

    listarEvent = () => {
        const abrirModal = (e) =>{
            $('#modalAgregarContactoEntidad').find('input[name=idEntidad]').val($(e.currentTarget).data('id'));
            if (this.seleccionarContacto) {
                $('#btnAgregarContactoEntidad').data('orden', $(e.currentTarget).data('orden'));
                $('#btnAgregarContactoEntidad').data('tipo', $(e.currentTarget).data('tipo'));
            }
            this.listar($(e.currentTarget).data('id'));
        }

        $('tbody').on('click', 'a.entidad', (e) => {
            abrirModal(e);
        });

        $('#btnDetallesEntidad').on('click', (e) => {
            abrirModal(e);
        });

        
    }
}
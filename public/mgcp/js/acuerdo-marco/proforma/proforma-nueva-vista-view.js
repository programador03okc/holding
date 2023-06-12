import ProformaView from './proforma-view.js'

export default class ProformaNuevaVistaView extends ProformaView{

    constructor(model, idUsuario) {
        super(model, idUsuario);
    }

    obtenerProformas(reiniciarPaginacion=false) {
        $('#btnBuscar').prop('disabled',true);
        $('#txtCriterioHidden').val($('#txtCriterio').val());
        const $contenedorProformas=$('#divContenedorProformas');
        const $bodyProformas = $('#divBodyProformas');
        const $footerProformas = $('#divFooterProformas');
        //$bodyProformas.html('<div class="text-center">Obteniendo datos...</div>');
        $contenedorProformas.LoadingOverlay("show", {
            imageAutoResize: true,
            progress: true,
            imageColor: "#3c8dbc",
            zIndex: 10
        });
        if (reiniciarPaginacion)
        {
            $('#txtNroPaginaHidden').val(1);
        }
        this.model.obtenerProformas($('#formFiltros').serialize()).then((respuesta) => {
            $bodyProformas.html(respuesta.body);
            $footerProformas.html(respuesta.footer);
        }).always(() => {
            $contenedorProformas.LoadingOverlay("hide", true);
            $('#btnBuscar').prop('disabled',false);
        }).fail(()=>{
            $bodyProformas.html('<div class="text-center">Hubo un problema al obtener las proformas. Por favor actualice la página e intente de nuevo</div>')
            $footerProformas.html('');
        });
    }

    mostrarDetallesProformaEvent() {
        $('#divBodyProformas').on('click', 'button.mostrar', (e) => {
            const $boton = $(e.currentTarget);
            $boton.removeClass('mostrar').addClass('ocultar').html('<span class="glyphicon glyphicon-minus"></span>')
            $boton.closest('div.panel').find('div.panel-body').fadeIn(300);
        });

        $('#divBodyProformas').on('click', 'button.ocultar', (e) => {
            const $boton = $(e.currentTarget);
            $boton.removeClass('ocultar').addClass('mostrar').html('<span class="glyphicon glyphicon-plus"></span>')
            $boton.closest('div.panel').find('div.panel-body').fadeOut(300);
        });
    }

    actualizarCantidadFiltrosAplicados() {
        $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
    }

    actualizarFiltrosEvent() {
        let actualizar = false;
        $('#modalFiltros').find('input[type=checkbox]').change(function () {
            actualizar = true;
        });

        $('#modalFiltros').find('input[type=text], select').change((e) => {
            if ($(e.currentTarget).hasClass('actualizar') || $(e.currentTarget).closest('div.form-group').find('input[type=checkbox]').is(':checked') == true) {
                actualizar = true;
            }
        });

        $("#modalFiltros").on("hidden.bs.modal", () => {
            if (actualizar) {
                actualizar=false;
                this.obtenerProformas(true);
            }
            this.actualizarCantidadFiltrosAplicados();
        });
    }

    paginarResultadoEvent() {
        $('#divFooterProformas').on('click','button.anterior',(e)=>{
            const $pagina=$('#divFooterProformas').find('select.pagina');
            if ($pagina.val()>1)
            {
                $pagina.val(parseInt($pagina.val())-1);
                $('#txtNroPaginaHidden').val($pagina.val());
                this.obtenerProformas();
            }
        });

        $('#divFooterProformas').on('click','button.siguiente',(e)=>{
            const $pagina=$('#divFooterProformas').find('select.pagina');
            if ($pagina.val()!=0 && $pagina.val()<$pagina.find('option').length)
            {
                $pagina.val(parseInt($pagina.val())+1);
                $('#txtNroPaginaHidden').val($pagina.val());
                this.obtenerProformas();
            }
        });

        $('#divFooterProformas').on('change','select.pagina',(e)=>{
            $('#txtNroPaginaHidden').val($(e.currentTarget).val());
            this.obtenerProformas();
        });
    }

    realizarBusquedaEvent() {
        $('#btnBuscar').on('click', (e) => {
            this.obtenerProformas(true);
        })

        $('#txtCriterio').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnBuscar').trigger('click');
            }
        });
    }

    gestionarComentariosEvent() {
        $('#divBodyProformas').on('click', 'a.comentarios', (e) => {
            e.preventDefault();
            const $elemento = $(e.currentTarget);
            const $modal = $('#modalComentarios');
            const $tbody = $('#tbodyComentarios');
            const $botonEnviar = $('#btnRegistrarComentario');
            $modal.find('h4:first').html(`Comentarios en proforma ${$elemento.data('proforma')}`);
            $botonEnviar.prop('disabled', true);
            $tbody.html('');
            Util.bloquearConSpinner($modal.find('div.mensaje'));
            $modal.modal('show');
            this.model.obtenerComentarios($elemento.data('id')).then((datos) => {
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
            this.model.registrarComentario($boton.data('id'), $textarea.val()).then((datos) => {
                $tbody.find('tr.sin-comentarios').remove();
                let fila = `<tr>
                    <td>${datos.usuario}</td>
                    <td class="text-justify">${$textarea.val()}</td>
                    <td class="text-center">${datos.fecha}</td>
                </tr>`;
                $tbody.append(fila);
                $textarea.val('');
                //$('#tableOrdenes').DataTable().ajax.reload();
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al registrar el comentario. Por favor vuelva a intentarlo');
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Registrar');
            });
        });
    }

    gestionarAnalisisEvent() {
        $('#divBodyProformas').on('click', 'a.analisis', (e) => {
            e.preventDefault();
            $("#formAnalisis")[0].reset();
            $("[name=id_proveedor]").val('default').selectpicker('refresh');
            $('textarea').text('');

            const $elemento = $(e.currentTarget);
            const $modal = $('#modalAnalisis');
            const $tbody = $('#tbodyAnalisis');
            const $botonEnviar = $('#btnRegistrarAnalisis');
            var $activo = $elemento.data('proforma') + '-' + $elemento.data('producto');
            var $mpgActivo = 'check-activo-' + $activo;

            if ($('.mpg-' + $activo).hasClass($mpgActivo)) {
                var id_proforma = $('.' + $mpgActivo).data('nro');
                var cod_proforma = $('.' + $mpgActivo).data('proforma');
                $modal.find('h4:first').html(`Análisis de proforma ${$elemento.data('proforma')}`);
                $botonEnviar.prop('disabled', false);
                $tbody.html('');
                this.activarTipoCambio(moment().format('YYYY-MM-DD'));
                $("[name=id_proforma_analisis]").val(0);
                $("[name=id_proforma]").val(id_proforma);
                $("[name=codigo_proforma]").val(cod_proforma);
                $("[name=cantidad]").val($elemento.data('cantidad'));
                Util.bloquearConSpinner($modal.find('div.mensaje'));
                $modal.modal('show');
                
                this.model.obtenerAnalisis(id_proforma).then((datos) => {
                    Util.notify(datos.alert, datos.message);
                    if (datos.response == 'ok') {
                        var precio_costo = $.number(datos.datos[0].precio_costo, 2, '.', ',');
                        var precio_dolares = $.number(datos.datos[0].precio_dolares, 2, '.', ',');
                        var precio_soles = $.number(datos.datos[0].precio_soles, 2, '.', ',');
                        var total = $.number(datos.datos[0].total, 2, '.', ',');
                        var margen = $.number(datos.datos[0].margen, 2, '.', ',');
    
                        $("[name=id_proforma_analisis]").val(datos.datos[0].id);
                        $("[name=id_proforma]").val(datos.datos[0].id_proforma);
                        $("[name=id_proveedor]").val(datos.datos[0].id_proveedor).selectpicker('refresh');
                        $("[name=tcSbs]").val(datos.datos[0].tipo_cambio);
                        $("#tcSbsText").text('TC: ' + datos.datos[0].tipo_cambio);
                        $("[name=id_producto_ext]").val(datos.datos[0].id_producto);
                        $("[name=part_number_ext]").val(datos.datos[0]['producto'].part_no);
                        $("[name=descripcion_ext]").text(datos.datos[0]['producto'].descripcion);
                        $("[name=costo_ext]").val(precio_costo);
                        $("[name=precio_sol_ext]").val(precio_soles);
                        $("[name=precio_dol_ext]").val(precio_dolares);
                        $("[name=total_ext]").val(total);
                        $("[name=margen_ext]").val(margen + '%');
                    }
                }).fail(() => {
                    alert('Hubo un problema al obtener el análisis. Por favor actualice la página e intente de nuevo');
                    $modal.modal('hide');
                }).always(() => {
                    Util.liberarBloqueoSpinner($modal.find('div.mensaje'));
                });
                Util.liberarBloqueoSpinner($modal.find('div.mensaje'));
            } else {
                Util.notify('info', 'Debe seleccionar una opción MPG');
            }
        });

        $('#btnRegistrarAnalisis').on('click',(e) => {
            const $boton = $(e.currentTarget);
            const $modal = $('#modalAnalisis');
            const $form = $("#formAnalisis").serializeArray();
            
            $boton.attr('disabled', true);
            $boton.html(Util.generarPuntosSvg() + 'Registrando');
            this.model.registrarAnalisis($form).then((datos) => {
                Util.notify(datos.alert, datos.message);
                if (datos.response == 'ok') {
                    $modal.modal("hide");
                }
            }).fail(() => {
                Util.notify('error', 'Hubo un problema al registrar el analisis. Por favor vuelva a intentarlo');
            }).always(() => {
                $boton.prop('disabled', false);
                $boton.html('Registrar');
            });
        });

        $('#txtPNext').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnPNext').trigger('click');
            }
        });

        $('#txtSolExt').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnCalcSolExt').trigger('click');
            }
        });

        $('#txtDolExt').on('keyup', (e) => {
            if (e.key == 'Enter') {
                $('#btnCalcDolExt').trigger('click');
            }
        });

        $('#btnPNext').on('click', (e) => {
            var part_no = $("[name=part_number_ext]").val();
            this.model.buscarProducto(part_no).then((datos) => {
                if (datos.response == 'ok') {
                    $("[name=id_producto_ext]").val(datos.producto.id);
                    $("[name=descripcion_ext]").text(datos.producto.descripcion);
                } else {
                    $("[name=id_producto_ext]").val('');
                    $("[name=descripcion_ext]").text('');
                    $("[name=part_number_ext]").val(partno);
                }
            });
        });

        $('#btnCalcSolExt').on('click', (e) => {
            this.calcular('soles');
        });

        $('#btnCalcDolExt').on('click', (e) => {
            this.calcular('dolares');
        });
    }

    descargarAnalisisEvent() {
        $('#btnDescargarAnalisis').on('click', (e) => {
            var $filtros = $('#formFiltros').serialize();
            this.model.filtrarAnalisisProformas($filtros).then((respuesta) => {
                if (respuesta.data == 'success') {
                    window.location.href = route('mgcp.acuerdo-marco.proformas.individual.descargar-analisis', $(e.currentTarget).data('tipo'));
                }
            });
        });
    }

    activarTipoCambio(fecha) {
        this.model.buscarTipoCambio(fecha).then((datos) => {
            $("[name=tcSbs]").val(datos);
            $("#tcSbsText").text('TC: ' + datos);
        });
    }

    calcular(moneda) {
        var costo = 0, precio = 0, prev = 0, cal_precio = 0;
        var cantidad = $("[name=cantidad]").val();
        var tcSbs = $("[name=tcSbs]").val();
    
        if (moneda == 'soles') {
            prev = $("[name=precio_sol_ext]").val();
            var precio_conv = prev.replace(' ', '');
            var nuevo_precio = precio_conv.replace(',', '');

            cal_precio = parseFloat(nuevo_precio) / parseFloat(tcSbs);
            var txt_tc_precio = $.number(cal_precio, 2, '.', ',');
            var txt_nv_precio = $.number(nuevo_precio, 2, '.', ',');

            $("[name=precio_dol_ext]").val(txt_tc_precio);
            $("[name=precio_sol_ext]").val(txt_nv_precio);
        } else  if (moneda == 'dolares') {
            prev = $("[name=precio_dol_ext]").val();
            var precio_conv = prev.replace(' ', '');
            var nuevo_precio = precio_conv.replace(',', '');

            cal_precio = parseFloat(nuevo_precio) * parseFloat(tcSbs);
            var txt_tc_precio = $.number(cal_precio, 2, '.', ',');
            var txt_nv_precio = $.number(nuevo_precio, 2, '.', ',');

            $("[name=precio_sol_ext]").val(txt_tc_precio);
            $("[name=precio_dol_ext]").val(txt_nv_precio);
        }

        costo = $("[name=costo_ext]").val();
        precio = $("[name=precio_dol_ext]").val();
    
        var dol_costo = costo.replace(',', '');
        var dol_precio = precio.replace(',', '');
    
        var total = parseFloat(dol_precio) * parseFloat(cantidad);
        var diff_dol = parseFloat(dol_precio) - parseFloat(dol_costo);
        var margen = (diff_dol * 100) / parseFloat(dol_precio);
    
        var txt_total = $.number(total, 2, '.', ',');
        var txt_margen = $.number(margen, 2, '.', ',');
        var txt_costo = $.number(dol_costo, 2, '.', ',');

        $("[name=costo_ext]").val(txt_costo);
        $("[name=total_ext]").val(txt_total);
        $("[name=margen_ext]").val(txt_margen + '%');
    }
}
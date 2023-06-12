$(document).ready(function () {
    $('.sidebar-mini').addClass('sidebar-collapse');
    Util.activarDatePicker();
    Util.seleccionarMenu(window.location);

    actualizarCantidadFiltrosAplicados();
    obtenerLista();
    activarTipoCambio(moment().format('YYYY-MM-DD'));

    // Eventos para mostrar y ocultar detalles
    $('#divBodyAnalisis').on('click', 'button.mostrar', (e) => {
        const $boton = $(e.currentTarget);
        $boton.removeClass('mostrar').addClass('ocultar').html('<span class="glyphicon glyphicon-minus"></span>')
        $boton.closest('div.panel').find('div.panel-body').fadeIn(300);
    });
    $('#divBodyAnalisis').on('click', 'button.ocultar', (e) => {
        const $boton = $(e.currentTarget);
        $boton.removeClass('ocultar').addClass('mostrar').html('<span class="glyphicon glyphicon-plus"></span>')
        $boton.closest('div.panel').find('div.panel-body').fadeOut(300);
    });

    // Eventos para la paginacion
    $('#divFooterAnalisis').on('click','button.anterior',(e)=>{
        const $pagina=$('#divFooterAnalisis').find('select.pagina');
        if ($pagina.val() > 1) {
            $pagina.val(parseInt($pagina.val()) - 1);
            $('#txtNroPaginaHidden').val($pagina.val());
            obtenerLista();
        }
    });
    $('#divFooterAnalisis').on('click','button.siguiente',(e)=>{
        const $pagina=$('#divFooterAnalisis').find('select.pagina');
        if ($pagina.val() != 0 && $pagina.val() < $pagina.find('option').length) {
            $pagina.val(parseInt($pagina.val())+1);
            $('#txtNroPaginaHidden').val($pagina.val());
            obtenerLista();
        }
    });
    $('#divFooterAnalisis').on('change','select.pagina',(e)=>{
        $('#txtNroPaginaHidden').val($(e.currentTarget).val());
        obtenerLista();
    });

    // Evento para los filtros
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
            actualizar = false;
            obtenerLista(true);
        }
        actualizarCantidadFiltrosAplicados();
    });

    // Evento para la busqueda
    $('#btnBuscar').on('click', (e) => {
        obtenerLista(true);
    })

    $('#txtCriterio').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnBuscar').trigger('click');
        }
    });

    $('#txtPN').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnPN').trigger('click');
        }
    });

    $('#txtPNext').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnPNext').trigger('click');
        }
    });

    $('#txtSol').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnCalcSol').trigger('click');
        }
    });

    $('#txtSolExt').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnCalcSolExt').trigger('click');
        }
    });

    $('#txtDol').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnCalcDol').trigger('click');
        }
    });

    $('#txtDolExt').on('keyup', (e) => {
        if (e.key == 'Enter') {
            $('#btnCalcDolExt').trigger('click');
        }
    });
});

function abrirModal() {
    $('#formRegistro')[0].reset();
    $('.selectpicker').val('default').selectpicker('refresh');
    $('textarea').text('');
    $('#modalData').modal('show');
}

function abrirFiltros() {
    $('#modalFiltros').modal('show');
}

function obtenerLista(paginacion = false) {
    $('#btnBuscar').prop('disabled', true);
    $('#txtCriterioHidden').val($('#txtCriterio').val());
    const $contenedorAnalisis=$('#divContenedorAnalisis');
    const $bodyAnalisis = $('#divBodyAnalisis');
    const $footerAnalisis = $('#divFooterAnalisis');
    
    if (paginacion) {
        $('#txtNroPaginaHidden').val(1);
    }
    
    $.ajax({
        type: "POST",
        url : route('mgcp.ordenes-compra.publicas.analisis-ocp.data-lista'),
        data: $('#formFiltros').serialize(),
        dataType: "JSON",
        beforeSend: function(){
            $contenedorAnalisis.LoadingOverlay("show", {
                imageAutoResize: true,
                progress: true,
                imageColor: "#3c8dbc",
                zIndex: 10
            });
        },
        success: function (response) {
            $bodyAnalisis.html(response.body);
            $footerAnalisis.html(response.foot);
            $contenedorAnalisis.LoadingOverlay("hide", true);
            $('#btnBuscar').prop('disabled', false);
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
    return false;
}

function actualizarCantidadFiltrosAplicados() {
    $('#spanCantFiltros').html($('#modalFiltros').find('input[type=checkbox]:checked').length);
}

function activarTipoCambio(fecha) {
    $.ajax({
        url: route('mgcp.cuadro-costos.ajustes.tipo-cambio.obtener-tc'),
        type: 'POST',
        data: { fecha: fecha, _token: token }
    }).then((data) => {
        tcSbs = data;
        $("#tcSbs").text('TC: ' + data);
    });

    calcular(1, 'int', 'soles');
}

function calcularImportes(valor, tipo, form) {
    var precio = 0, cprecio = 0;
    var cantidad = 0;
    var costo = 0, ccosto = 0;
    if (valor == '' || valor == undefined || valor == null) {
        valor = 0;
    }

    if (form == 1) {
        ccosto = $("[name=costo]").val();
        cprecio = $("[name=precio_sol]").val();
    } else {
        ccosto = $("[name=costo_ext]").val();
        cprecio = $("[name=precio_sol_ext]").val();
    }

    if (tipo == "precio") {
        precio = valor;
        cantidad = $("[name=cantidad]").val();
        costo = ccosto
    } else if (tipo == "cantidad") {
        precio = $("[name=precio_sol]").val();
        cantidad = valor;
        costo = ccosto
    } else if(tipo == "costo") {
        precio = $("[name=precio_sol]").val();
        cantidad = $("[name=cantidad]").val();
        costo = valor;
    } else {
        precio = $("[name=precio_sol]").val();
        cantidad = $("[name=cantidad]").val();
        costo = ccosto
    }

    var precio_dol = parseFloat(precio) / parseFloat(tcSbs);
    var total = precio_dol * parseFloat(cantidad);
    var diff_dol = precio_dol - parseFloat(costo);
    var margen = (diff_dol * 100) / precio_dol;

    var txt_dolares = $.number(precio_dol, 2, '.', ',');
    var txt_total = $.number(total, 2, '.', ',');
    var txt_margen = $.number(margen, 2, '.', ',');

    if (form == 1) {
        $("[name=precio_dol]").val(txt_dolares);
        $("[name=total]").val(txt_total);
        $("[name=margen]").val(txt_margen + '%');
    } else {
        $("[name=precio_dol_ext]").val(txt_dolares);
        $("[name=total_ext]").val(txt_total);
        $("[name=margen_ext]").val(txt_margen + '%');
    }
}

function calcular(tipo, modelo, moneda) {
    var costo = 0, precio = 0, prev = 0, cal_precio = 0;
    var cantidad = $("[name=cantidad]").val();

    if (modelo == 'int') {
        if (moneda == 'soles') {
            prev = $("[name=precio_sol]").val();
            var precio_conv = prev.replace(' ', '');
            var nuevo_precio = precio_conv.replace(',', '');
            
            cal_precio = parseFloat(nuevo_precio) / parseFloat(tcSbs);
            var txt_nv_precio = $.number(nuevo_precio, 2, '.', ',');
            var txt_tc_precio = $.number(cal_precio, 2, '.', ',');

            $("[name=precio_dol]").val(txt_tc_precio);
            $("[name=precio_sol]").val(txt_nv_precio);
        } else  if (moneda == 'dolares') {
            prev = $("[name=precio_dol]").val();
            var precio_conv = prev.replace(' ', '');
            var nuevo_precio = precio_conv.replace(',', '');

            cal_precio = parseFloat(nuevo_precio) * parseFloat(tcSbs);
            var txt_tc_precio = $.number(cal_precio, 2, '.', ',');
            var txt_nv_precio = $.number(nuevo_precio, 2, '.', ',');
            
            $("[name=precio_sol]").val(txt_tc_precio);
            $("[name=precio_dol]").val(txt_nv_precio);
        }        
    } else if (modelo == 'ext') {
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
    }

    if (tipo == 1) {
        costo = $("[name=costo]").val();
        precio = $("[name=precio_dol]").val();
    } else if (tipo == 2) {
        costo = $("[name=costo_ext]").val();
        precio = $("[name=precio_dol_ext]").val();
    }

    dol_costo = costo.replace(',', '');
    dol_precio = precio.replace(',', '');

    var total = parseFloat(dol_precio) * parseFloat(cantidad);
    var diff_dol = parseFloat(dol_precio) - parseFloat(dol_costo);
    var margen = (diff_dol * 100) / parseFloat(dol_precio);

    var txt_total = $.number(total, 2, '.', ',');
    var txt_margen = $.number(margen, 2, '.', ',');
    var txt_costo = $.number(dol_costo, 2, '.', ',');

    if (tipo == 1) {
        $("[name=costo]").val(txt_costo);
        $("[name=total]").val(txt_total);
        $("[name=margen]").val(txt_margen + '%');
    } else if (tipo == 2) {
        $("[name=costo_ext]").val(txt_costo);
        $("[name=total_ext]").val(txt_total);
        $("[name=margen_ext]").val(txt_margen + '%');
    }
}

function buscarProducto(tipo) {
    var partno = (tipo == 1) ? $("[name=part_number]").val() : $("[name=part_number_ext]").val();
    $.ajax({
        url: route('mgcp.ordenes-compra.publicas.analisis-ocp.busqueda-producto'),
        type: 'POST',
        data: { partno: partno, _token: token },
        success: function (response) {
            if (response.response == 'ok') {
                var datax = response.producto;
                if (tipo == 1) {
                    $("[name=id_producto]").val(datax.id);
                    $("[name=descripcion]").text(datax.descripcion);
                    $("[name=part_number_ext]").val(datax.part_no);
                } else {
                    $("[name=id_producto_ext]").val(datax.id);
                    $("[name=descripcion_ext]").text(datax.descripcion);
                }
                Util.notify('success', 'Producto encontrado por Part Number');
            } else {
                $("[name=id_producto]").val('');
                $("[name=id_producto_ext]").val('');
                $("[name=descripcion]").text('');
                $("[name=descripcion_ext]").text('');
                $("[name=part_number_ext]").val(partno);
                Util.notify('info', 'Producto no encontrado');
            }
        }
    });
    return false;
}

function buscarProductoId(tipo, valor) {
    $.ajax({
        url: route('mgcp.acuerdo-marco.productos.obtener-detalles-por-id'),
        type: 'POST',
        data: { idProducto: valor, _token: token },
        success: function (response) {
            if (tipo == 1) {
                $("[name=id_producto]").val(response.id);
                $("[name=descripcion]").text(response.descripcion);
                $("[name=part_number]").val(response.part_no);
            } else {
                $("[name=id_producto_ext]").val(response.id);
                $("[name=descripcion_ext]").text(response.descripcion);
                $("[name=part_number_ext]").val(response.part_no);
            }
        }
    });
    return false;
}

function guardarRegistro() {
    var cant = $("[name=cantidad]").val();
    var enti = $("[name=id_entidad]").val();
    var empr = $("[name=id_empresa]").val();
    var part = $("[name=part_number]").val();
    var tota = $("[name=total]").val();
    var data = $('#formRegistro').serializeArray();
    var total = parseFloat(tota.replace(',' , ''));

    if (cant > 0) {
        if (enti > 0) {
            if (empr > 0) {
                if (part) {
                    if (total) {
                        $.ajax({
                            type: "POST",
                            url : route('mgcp.ordenes-compra.publicas.analisis-ocp.registrar'),
                            data: data,
                            dataType: "JSON",
                            success: function (response) {
                                if (response.response == 'ok') {
                                    obtenerLista();
                                    $("#modalData").modal("hide");
                                }
                                Util.notify(response.alert, response.message);
                            }
                        }).fail( function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                        });
                    } else {
                        Util.notify('warning', 'Debe ingresar el precio cotizado por la empresa');
                        $("[name=precio_sol]").focus();   
                    }
                } else {
                    Util.notify('warning', 'Debe ingresar un PartNumber');
                    $("[name=part_number]").focus(); 
                }
            } else {
                Util.notify('warning', 'Debe seleccionar una empresa');
                $("[name=id_empresa]").focus();
            }
        } else {
            Util.notify('warning', 'Debe seleccionar una entidad');
            $("[name=id_entidad]").focus();
        }
    } else {
        Util.notify('warning', 'Debe ingresar la cantidad mayor a 0');
        $("[name=cantidad]").focus();
    }

    return false;
}

function editar(id) {
    $.ajax({
        type: "POST",
        url : route('mgcp.ordenes-compra.publicas.analisis-ocp.editar'),
        data: {
            _token: token,
            id: id,
        },
        dataType: "JSON",
        success: function (response) {
            var costo = $.number(response.precio_costo, 2, '.', ',');
            var precio_sol = $.number(response.precio_soles, 2, '.', ',');
            var precio_dol = $.number(response.precio_dolares, 2, '.', ',');
            var total = $.number(response.total, 2, '.', ',');
            var margen = $.number(response.margen, 2, '.', ',');
            var costo_ext = $.number(response.precio_costo_ext, 2, '.', ',');
            var precio_sol_ext = $.number(response.precio_soles_ext, 2, '.', ',');
            var precio_dol_ext = $.number(response.precio_dolares_ext, 2, '.', ',');
            var total_ext = $.number(response.total_ext, 2, '.', ',');
            var margen_ext = $.number(response.margen_ext, 2, '.', ',');
            
            buscarProductoId(1, response.id_producto);
            buscarProductoId(2, response.id_producto_ext);
            activarTipoCambio(response.fecha_convocatoria);

            $("[name=id_ocp]").val(response.id);
            $("[name=fecha]").val(response.fecha);
            $("[name=fecha_convocatoria]").val(response.fecha_convocatoria);
            $("[name=cantidad]").val(response.cantidad);
            $('[name=id_entidad]').val(response.id_entidad).selectpicker('refresh');
            $("[name=id_empresa]").val(response.id_empresa);
            $("[name=id_proveedor]").val(response.id_proveedor).selectpicker('refresh');

            $("[name=costo]").val(costo);
            $("[name=precio_sol]").val(precio_sol);
            $("[name=precio_dol]").val(precio_dol);
            $("[name=total]").val(total);
            $("[name=margen]").val(margen);

            $("[name=costo_ext]").val(costo_ext);
            $("[name=precio_sol_ext]").val(precio_sol_ext);
            $("[name=precio_dol_ext]").val(precio_dol_ext);
            $("[name=total_ext]").val(total_ext);
            $("[name=margen_ext]").val(margen_ext);

            $('#modalData').modal('show');
        }
    }).fail( function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    });
}

function exportar() {
    var link = route('mgcp.ordenes-compra.publicas.analisis-ocp.exportar');
    window.location.href = link;
}
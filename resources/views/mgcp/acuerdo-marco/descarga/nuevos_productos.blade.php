@extends('mgcp.layouts.app')

@section('cabecera') Descargar nuevos productos @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Descargar</li>
    <li class="active">Nuevos productos</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-header">
        <h3 class="box-title">Configuración</h3>
    </div>
    <div class="box-body">
        
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-1 control-label">Empresa:</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="selectEmpresa" name="empresa">
                                @foreach($empresas as $empresa)
                                <option value="{{$empresa->id}}">{{$empresa->empresa}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="col-sm-1 control-label">Tipo:</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="selectTipo">
                                <option value="productos_acuerdo_vigente" selected="">Acuerdo vigente</option>
                                <option value="productos_nuevo_acuerdo">Nuevo acuerdo</option>
                            </select>

                        </div>
                        <div class="col-sm-2">
                            <button id="btnObtenerAcuerdos" class="btn btn-default btn-flat">Obtener acuerdos</button>
                        </div>
                        <label class="col-sm-1 control-label">Ind. pág:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control text-center" id="txtPagina" name="pagina" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label">Acuerdo:</label>
                        <div class="col-sm-5" id="tdAcuerdos">
                            <div class="form-control-static"><span class="text-success">En espera...</span></div>
                        </div>
                        <div class="col-sm-2"><button id="btnIniciar" disabled class="btn btn-primary btn-flat">Iniciar descarga</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header">
        <h3 class="box-title">Progreso</h3>
    </div>
    <div class="box-body">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th class="text-center">Catálogos</th>
                    <th class="text-center">Categorías</th>
                    <th class="text-center">Catálogo actual</th>
                    <th class="text-center">Categoría actual</th>
                    <th class="text-center">Productos procesados</th>
                    <th class="text-center">Progreso total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="tdCatalogos" style="width: 20%"></td>
                    <td id="tdCategorias" style="width: 20%"></td>
                    <td id="tdCatalogoActual" class="text-center"></td>
                    <td id="tdCategoriaActual" class="text-center"></td>
                    <td id="tdProductosProcesados"></td>
                    <td id="tdProgresoTotal"></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

<div class="box box-solid">
    <div class="box-header">
        <h3 class="box-title">Data</h3>
    </div>
    <div class="box-body">
        <table style="width: 100%; font-size: small" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="text-center">N°</th>
                    <th class="text-center">ID</th>
                    <th class="text-center" style="width: 40%">Descripción</th>
                    <th class="text-center">Imagen</th>
                    <th class="text-center">Ficha</th>
                    <th class="text-center">Moneda</th>
                    <th class="text-center">Resultado</th>
                </tr>
            </thead>
            <tbody id="tbodyProductos">

            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src='{{ asset("mgcp/js/util.js") }}'></script>
<script>
    $(document).ready(function() {

        Util.seleccionarMenu(window.location);
        var totalCatalogos;
        var totalCategorias;
        var filaActual;
        var totalFilas;
        var indiceCatalogo;
        var indiceCategoria;

        var $tbodyProductos = $('#tbodyProductos');
        var $tdCatalogos = $('#tdCatalogos');
        var $tdCatalogoActual = $('#tdCatalogoActual');
        var $tdCategorias = $('#tdCategorias');
        var $tdCategoriaActual = $('#tdCategoriaActual');
        var $tdProductosProcesados = $('#tdProductosProcesados');
        var $tdProgresoTotal = $('#tdProgresoTotal');
        var $botonObtenerAcuerdos = $('#btnObtenerAcuerdos');
        var $botonIniciar = $('#btnIniciar');
        var $tdAcuerdos = $('#tdAcuerdos');
        var $selectEmpresa = $('#selectEmpresa');
        var $selectTipo = $('#selectTipo');
        var $selectAcuerdos;
        var $selectCatalogos;
        var $selectCategorias;

        $botonObtenerAcuerdos.click(function() {
            $tdAcuerdos.html('<div class="form-control-static">Obteniendo datos...</div>');
            $botonObtenerAcuerdos.prop('disabled', true);
            $botonIniciar.prop('disabled', true);
            $selectEmpresa.prop('disabled', true);
            $selectTipo.prop('disabled', true);
            $.ajax({
                url: "{{route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos')}}",
                type: 'post',
                dataType: 'json',
                data: {
                    idEmpresa: $selectEmpresa.val(),
                    pagina: $selectTipo.val(),
                    _token: "{{csrf_token()}}"
                },
                success: function(resultado) {
                    if (resultado.mensaje == 'ok') {
                        if (resultado.data.length > 0) {
                            var select = '<select class="form-control" id="selectAcuerdos">';
                            for (var indice in resultado.data) {
                                select += '<option value="' + resultado.data[indice].id + '">' + resultado.data[indice].descripcion + '</option>';
                            }
                            select += '</select>';
                            $tdAcuerdos.html(select);
                            $selectAcuerdos = $('#selectAcuerdos');
                            $botonIniciar.prop('disabled', false);
                        } else {
                            $tdAcuerdos.html('<span class="text-danger">Sin acuerdos. Operación no puede continuar.</span>');
                        }
                    } else {
                        $tdAcuerdos.html('<span class="text-danger">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</span>');
                    }

                },
                error: function() {
                    $tdAcuerdos.html('<div class="form-control-static"><span class="text-danger">Error al obtener acuerdos. Por favor inténtelo de nuevo.</span></div>');
                },
                complete: function() {
                    $botonObtenerAcuerdos.prop('disabled', false);
                    $botonIniciar.prop('disabled', false);
                    $selectEmpresa.prop('disabled', false);
                    $selectTipo.prop('disabled', false);
                }
            });
        });

        $('#btnContinuar').click(function() {
            filaActual = totalFilas;
        });

        $botonIniciar.click(function() {
            indiceCatalogo = 0;
            indiceCategoria = 0;
            $selectEmpresa.prop('disabled', true);
            $botonIniciar.prop('disabled', true);
            $botonObtenerAcuerdos.prop('disabled', true);
            $selectAcuerdos.prop('disabled', true);
            $selectTipo.prop('disabled', true);
            obtenerCatalogos();
            $tdCatalogoActual.html('Por favor espere...');
            $tdCategorias.html('Por favor espere...');
            $tdCategoriaActual.html('Por favor espere...');
            $tdProductosProcesados.html('Por favor espere...');
            $tdProgresoTotal.html('Por favor espere...');
        });

        function obtenerCatalogos() {
            $tdCatalogos.html('Obteniendo datos...');
            $.ajax({
                url: "{{route('mgcp.acuerdo-marco.peru-compras.obtener-catalogos')}}",
                type: 'post',
                data: {
                    idEmpresa: $selectEmpresa.val(),
                    idAcuerdo: $selectAcuerdos.val(),
                    _token: "{{csrf_token()}}"
                },
                success: function(resultado) {
                    if (resultado.mensaje == 'ok') {
                        if (resultado.data.length > 0) {
                            var cadena = '<select class="form-control input-sm" id="selectCatalogos" size="3">';
                            for (var indice in resultado.data) {
                                cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>'; //datos[indice].descripcion + ', ';
                            }
                            cadena += '</select>';
                            $tdCatalogos.html(cadena);
                            $selectCatalogos = $('#selectCatalogos');
                            totalCatalogos = $selectCatalogos.find('option').length;
                            obtenerCategorias()
                        } else {
                            $tdCatalogos.html('<span class="text-danger">Sin catálogos. Operación no puede continuar.</span>');
                        }
                    } else {
                        $tdCatalogos.html('<span class="text-danger">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</span>');
                    }
                },
                error: function() {
                    $tdCatalogos.html('<div class="form-control-static"><span class="text-danger">Error al obtener catálogos. Reintentando...</span></div>');
                    obtenerCatalogos();
                }
            });
        }

        function obtenerCategorias() {
            $tdCatalogoActual.html($selectCatalogos.find('option:eq(' + indiceCatalogo + ')').html() + ' (' + (indiceCatalogo + 1) + ' de ' + totalCatalogos + ')');
            $tdCategorias.html('Obteniendo datos...');
            $.ajax({
                url: "{{route('mgcp.acuerdo-marco.peru-compras.obtener-categorias')}}",
                type: 'post',
                data: {
                    idEmpresa: $selectEmpresa.val(),
                    idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                    _token: "{{csrf_token()}}"
                },
                success: function(resultado) {
                    if (resultado.mensaje == 'ok') {
                        indiceCategoria = 0;
                        if (resultado.data.length > 0) {
                            var cadena = '<select class="form-control input-sm" id="selectCategorias" size="5">';
                            for (var indice in resultado.data) {
                                cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>'; //datos[indice].descripcion + ', ';
                            }
                            cadena += '</select>';
                            $tdCategorias.html(cadena);
                            $selectCategorias = $('#selectCategorias');
                            totalCategorias = $selectCategorias.find('option').length;
                            obtenerProductos();
                        } else {
                            $tdCategorias.html('<span class="text-danger">Sin categorías. Operación no puede continuar.</span>');
                        }
                    } else {
                        $tdCatalogos.html('<span class="text-danger">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</span>');
                    }
                },
                error: function() {
                    $tdCatalogos.html('<div class="form-control-static"><span class="text-danger">Error al obtener categorías. Reintentando...</span></div>');
                    obtenerCategorias();
                }
            });
        }

        function obtenerProductos() {
            $tdCategoriaActual.html($selectCategorias.find('option:eq(' + indiceCategoria + ')').html() + ' (' + (indiceCategoria + 1) + ' de ' + totalCategorias + ')');
            $tdProductosProcesados.html('Obteniendo datos...');

            $.ajax({
                url: "{{route('mgcp.acuerdo-marco.descargar.nuevos-productos.obtener-productos')}}",
                type: 'post',
                data: {
                    idEmpresa: $selectEmpresa.val(),
                    idAcuerdo: $selectAcuerdos.val(),
                    idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                    idCategoria: $selectCategorias.find('option:eq(' + indiceCategoria + ')').val(),
                    tipoProforma: $selectTipo.val(),
                    pagina: $('#txtPagina').val(),
                    _token: "{{csrf_token()}}"
                },
                success: function(datos) {
                    $tbodyProductos.html(datos);
                    if (datos.includes('Error') == false) {
                        filaActual = 0;
                        totalFilas = $tbodyProductos.find('tr').length;
                        procesarProductos();
                    }
                },
                error: function() {
                    $tbodyProductos.html('<tr><td colspan="7" class="text-center">Error al obtener los productos. Reintentando....</td></tr>');
                    obtenerProductos();
                }
            });
        }


        function procesarProductos() {
            if (filaActual < totalFilas) {
                var $fila = $tbodyProductos.find('tr:eq(' + filaActual + ')');
                $fila.find('td.resultado').html('Procesando...');
                $.ajax({
                    url: "{{route('mgcp.acuerdo-marco.descargar.nuevos-productos.procesar')}}",
                    type: 'post',
                    data: {
                        acuerdo: $selectAcuerdos.find('option:selected').val(),
                        catalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                        categoria: $selectCategorias.find('option:eq(' + indiceCategoria + ')').val(),
                        idPc: $fila.find('td.idPc').html(),
                        descripcion: $fila.find('td.descripcion').html(),
                        imagen: $fila.find('td.imagen').html(),
                        ficha: $fila.find('td.ficha').html(),
                        moneda: $fila.find('td.moneda').html(),
                        _token: "{{csrf_token()}}"
                    },
                    success: function(datos) {
                        $fila.find('td.resultado').html('<span class="text-success">Procesado</span>');
                    },
                    error: function() {
                        $fila.find('td.resultado').html('<span class="text-danger">Error</span>');
                        console.log("Error en: " + $fila.find('td.idPc').html() + " - " + $fila.find('td.descripcion').html());
                    },
                    complete: function() {
                        filaActual++;
                        mostrarProgreso();
                        procesarProductos();
                    }
                });
            } else {
                indiceCategoria++;
                if (indiceCategoria < totalCategorias) {
                    obtenerProductos();
                } else {
                    indiceCatalogo++;
                    if (indiceCatalogo < totalCatalogos) {
                        obtenerCategorias();
                    } else {
                        $tdProductosProcesados.html('<span class="text-success">Fin del proceso.</span>');
                        $tdProgresoTotal.html('Catálogos procesados: ' + indiceCatalogo + ' de ' + totalCatalogos);
                    }
                }

            }

        }

        function mostrarProgreso() {
            $tdProductosProcesados.html('Procesando producto ' + (filaActual + 1) + ' de ' + totalFilas);
            $tdProgresoTotal.html('Catálogos procesados: ' + indiceCatalogo + ' de ' + totalCatalogos);
        }

    });
</script>
@endsection
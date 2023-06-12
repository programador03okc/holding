@extends('mgcp.layouts.app')
@section('estilos')
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        table img {
            display: none;
        }
        .box-body h5{
            font-weight: 600;
        }
        .box-body h5 span{
            font-weight: normal;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        body .bootstrap-select .dropdown-menu {
            min-width: 100% !important;
            max-width: 100% !important;
            font-size: 12.5px;
            overflow: visible;
            overflow-x: scroll !important;
        }
    </style>
@endsection

@section('cabecera')
    Publicar plazos de entrega
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Publicar</li>
        <li class="active">Plazos de entrega</li>
    </ol>
@endsection

@section('cuerpo')

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Configuración</h3>
        </div>
        <div class="box-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Empresa</h5>
                    <div class="input-group">
                        <select class="form-control" id="selectEmpresa" name="empresa">
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" data-pc="{{ $empresa->id_pc }}">{{ $empresa->empresa }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-flat" onclick="obtenerAcuerdos();">Obtener Acuerdos</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Acuerdos <span class="pull-right text-success" id="MessageAcuerdo">En espera...</span></h5>
                    <select class="form-control" id="selectAcuerdo" disabled></select>
                </div>
                <div class="col-md-3">
                    <h5>Categorías <span class="pull-right text-success" id="MessageCategoria"></span></h5>
                    <div class="input-group">
                        <div id="BaseCategoria">
                            <select class="form-control" id="selectCategoriaView" disabled><option value="">No hay selección</option></select>
                            <div id="divCategorias"></div>
                        </div>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-flat" onclick="obtenerAcuerdos();">Productos</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                {{--  <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Tipo</h5>
                            <select class="form-control" name="tipo" id="tipo">
                                <option value="1">Lista de productos</option>
                                <option value="2">Descripción del producto</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <h5>Regiones</h5>
                            <select id="selectRegion" name="region" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple>
                                @foreach ($regiones as $region)
                                    <option value="{{ $region->id_portal }}">{{ $region->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>  --}}
                <div class="col-md-2">
                    <h5>Regiones</h5>
                    <select id="selectRegion" name="region" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple>
                        @foreach ($regiones as $region)
                            <option value="{{ $region->id_portal }}">{{ $region->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <h5>Productos <span class="pull-right text-success" id="MessageProducto"></span></h5>
                    <div id="BaseProductos">
                        <select class="form-control" id="selectProductoView"><option value="">No hay selección</option></select>
                        <div id="divProductos"></div>
                    </div>
                    <input type="text" class="form-control input-sm d-none" name="producto_nombre" id="producto_nombre">
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Plazo</h5>
                            <input type="number" class="form-control text-center" id="txtPlazo" value="60">
                        </div>
                        <div class="col-md-4 text-center">
                            <button class="btn btn-primary btn-flat" id="btnIniciar" style="margin-top: 34px;">Iniciar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Progreso</h3>
        </div>
        <div class="box-body">
            <table class="table table-condensed" style="width: 100%">
                <thead>
                    <tr>
                        <th style="width: 25%" class="text-center">Categoría actual</th>
                        <th style="width: 25%" class="text-center">Región actual</th>
                        <th style="width: 25%" class="text-center">Provincia actual</th>
                        <th style="width: 25%" class="text-center">Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="tdCategoriaActual" class="text-center"></td>
                        <td id="tdRegionActual" class="text-center"></td>
                        <td id="tdProvinciaActual" class="text-center"></td>
                        <td id="tdProgreso"></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('mgcp/js/util.js') }}"></script>
    <script src="{{ asset('mgcp/js/timer.js') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/peru-compras.js') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/producto/catalogo.js') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/producto/categoria.js') }}"></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            $(".sidebar-mini").addClass("sidebar-collapse");

            let indiceCategoria;
            let indiceProvincia;
            let indiceRegion;

            let listaCategorias;
            let listaRegiones;
            let listaProvincias;
            let idAcuerdo;
            let plazo;

            const $selectEmpresa = $('#selectEmpresa');
            const $selectAcuerdos = $('#selectAcuerdo');
            const $selectCategorias = $('#selectCategoria');
            const $selectCategoriaView = $('#selectCategoriaView');
            const $selectProductoView = $('#selectProductoView');
            const $selectTipo = $('#tipo');

            const $spanAcuerdos = $('#MessageAcuerdo');
            const $spanCategorias = $('#MessageCategoria');
            const $spanProductos = $('#MessageProducto');

            const $tdCategoriaActual = $('#tdCategoriaActual');
            const $tdRegionActual = $('#tdRegionActual');
            const $tdProvinciaActual = $('#tdProvinciaActual');
            const $tdProgreso = $('#tdProgreso');
            const $btnIniciar = $('#btnIniciar');

            const catalogo = new Catalogo("{{ csrf_token() }}");
            const categoria = new Categoria("{{ csrf_token() }}");
            const peruCompras = new PeruCompras("{{ csrf_token() }}");
            let timer;

            obtenerAcuerdos = () => {
                var route = "{{ route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos') }}";
                $('#selectCategoria').selectpicker('destroy');
                $('#selectCategoria').val('default').selectpicker("refresh");
                $spanCategorias.text('En espera...');
                $selectEmpresa.prop('disabled', true);
                $spanAcuerdos.text('Obteniendo acuerdos...');
                $btnIniciar.prop('disabled', true);

                $.when(peruCompras.obtenerAcuerdos($selectEmpresa.val(), 'mejorar_plazo', route)).then(function (respuesta) {
                    let acuerdos = '';
                    for (let indice in respuesta.data) {
                        acuerdos += `<option value="${respuesta.data[indice].id}">${respuesta.data[indice].descripcion}</option>`;
                    }
                    
                    $btnIniciar.prop('disabled', false);
                    $selectEmpresa.prop('disabled', false);
                    $selectAcuerdos.html(acuerdos).trigger('change');
                    $selectAcuerdos.removeAttr('disabled');
                    $spanAcuerdos.text('');
                    obtenerCategoriasPorAcuerdo($selectAcuerdos.find('option:selected').text());
                }, function() {
                    obtenerAcuerdos();
                });
            }

            obtenerCategoriasPorAcuerdo = (descripcionAm, tipo = 0) => {
                const $divCategorias = $('#divCategorias');
                $divCategorias.find('select').val('default').selectpicker("refresh");
                $spanCategorias.text('Obteniendo categorías...');

                $.ajax({
                    url: route('mgcp.acuerdo-marco.publicar.plazos-entrega.obtener-categorias-por-acuerdo'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        descripcionAm: descripcionAm,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        let filas = `<select name="categoria" id="selectCategoria" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple>`;
                        for (let indice in data) {
                            filas+=`<option value="${data[indice].id}">${data[indice].categoria}</option>`;
                        }
                        filas += '</select>';

                        if (tipo == 1) {
                            $selectCategoriaView.addClass('d-none')
                        }
                        $divCategorias.html(filas);
                        $divCategorias.find('select').selectpicker('render');
                        $spanCategorias.text('');
                    }
                });
            }

            $selectEmpresa.change(() => {
                obtenerAcuerdos();
            });

            $selectAcuerdos.on('change', function() {
                obtenerCategoriasPorAcuerdo($(this).find('option:selected').text(), 1);
            });

            $selectTipo.on('change', function() {
                if ($(this).val() == 1) {
                    $('#BaseProductos').removeClass('d-none');
                    $('#producto_nombre').addClass('d-none');
                    obtenerProductos();
                } else {
                    $('#BaseProductos').addClass('d-none');
                    $('#producto_nombre').removeClass('d-none');
                }
            });

            obtenerProvincias = () => {
                var route = "{{ route('mgcp.acuerdo-marco.peru-compras.obtener-provincias') }}";
                if (indiceRegion == listaRegiones.length) {
                    indiceCategoria++;
                    indiceRegion = 0;
                    if (indiceCategoria == listaCategorias.length) {
                        $tdProgreso.html('<span class="text-success">Fin del proceso</span>');
                        timer.stop();
                    } else {
                        obtenerProvincias();
                    }
                } else {
                    indiceProvincia = 0;
                    listaProvincias = [];
                    $tdRegionActual.html(
                        `${listaRegiones[indiceRegion].nombre} (${indiceRegion+1} de ${listaRegiones.length})`
                    );
                    $tdCategoriaActual.html(
                        `${listaCategorias[indiceCategoria].descripcion} (${indiceCategoria+1} de ${listaCategorias.length})`
                    );
                    $tdProvinciaActual.html('Obteniendo provincias...');
                    $.when(peruCompras.obtenerProvincias($selectEmpresa.val(), listaRegiones[indiceRegion].id, route)).then(function (respuesta) {
                        if (respuesta.tipo == 'danger') {
                            $tdProvinciaActual.html(
                                `<span class="text-danger">${respuesta.mensaje}</span>`);
                        } else {
                            for (let indice in respuesta.data) {
                                listaProvincias.push({id: respuesta.data[indice].id, nombre: respuesta.data[indice].nombre});
                            }
                            $tdProgreso.html('Procesando...');
                            procesar();
                        }
                    }, function() {
                        obtenerProvincias();
                    });
                }
            }

            obtenerRegiones = () => {
                indiceRegion = 0;
                listaRegiones = [];
                $('#selectRegion option:selected').each(function() {
                    listaRegiones.push({
                        id: $(this).val(),
                        nombre: $(this).text()
                    });
                });
                $tdRegionActual.html(`${ listaRegiones[indiceRegion].nombre } (${ indiceRegion + 1 } de ${ listaRegiones.length })`);
            };

            obtenerCategorias = () => {
                indiceCategoria = 0;
                listaCategorias = [];
                $('#selectCategoria option:selected').each(function() {
                    listaCategorias.push({
                        id: $(this).val(),
                        descripcion: $(this).text()
                    });
                });
                $tdCategoriaActual.html(`${ listaCategorias[indiceCategoria].descripcion } (${ indiceCategoria + 1 } de ${ listaCategorias.length })`);
            };

            obtenerProductos = () => {
                const $divProductos = $('#divProductos');
                $divProductos.find('select').val('default').selectpicker("refresh");
                $spanProductos.text('Obteniendo productos...');

                var newListaCategorias = [];
                $('#selectCategoria option:selected').each(function() {
                    newListaCategorias.push($(this).val());
                });

                $.ajax({
                    url: route('mgcp.acuerdo-marco.publicar.plazos-entrega.obtener-productos-por-categoria'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        categoria: newListaCategorias,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        let filas = `<select name="producto" id="selectProducto" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true">`;
                        for (let indice in data) {
                            filas+=`<option value="${ data[indice].id }">${ data[indice].descripcion }</option>`;
                        }
                        filas += '</select>';
                        $selectProductoView.addClass('d-none');
                        $divProductos.html(filas);
                        $divProductos.find('select').selectpicker('render');
                        $spanProductos.text('');
                    }
                });
            }

            procesar = () => {
                timer.reset(80000);
                $tdProvinciaActual.html(`${ listaProvincias[indiceProvincia].nombre } (${ indiceProvincia + 1 } de ${ listaProvincias.length })`);
                //let $descripcion = ($selectTipo.val() == 1) ? $('#selectProducto').val() : $('#producto_nombre').val();
                $.ajax({
                    url: "{{ route('mgcp.acuerdo-marco.publicar.plazos-entrega.procesar') }}",
                    //url: route('test-plazos'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        idAcuerdo: idAcuerdo,
                        idCategoria: listaCategorias[indiceCategoria].id,
                        idProvincia: listaProvincias[indiceProvincia].id,
                        //tipoProducto: $selectTipo.val(),
                        //descripcion: $descripcion,
                        plazo: plazo,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.tipo == "success") {
                            indiceProvincia++;
                            if (indiceProvincia == listaProvincias.length) {
                                indiceRegion++;
                                obtenerProvincias();
                            } else {
                                procesar();
                            }
                        }
                    },
                    error: function() {
                        console.log("ERROR en procesar");
                        procesar();
                    }
                });
            };

            $('#btnIniciar').click(function() {
                timer = new Timer(function() {
                    console.log("Función disparada el " + new Date().toLocaleTimeString());
                    procesar();
                }, 40000);
                //timer.start();
                //console.log("Llamada a iniciar");
                if ($('#selectRegion option:selected').length == 0) {
                    alert("Seleccione al menos una región para continuar");
                    return;
                }
                idAcuerdo = $('#selectAcuerdo').val();
                idAcuerdo = idAcuerdo.substring(idAcuerdo.indexOf("-") + 1);
                idAcuerdo = idAcuerdo.substring(0, idAcuerdo.indexOf("-"));
                plazo = $('#txtPlazo').val();
                $selectEmpresa.prop('disabled', true);
                $('#selectAcuerdo').prop('disabled', true);
                $('#selectRegion').prop('disabled', true);
                $(this).prop('disabled', true);
                $tdProgreso.html('Por favor espere...');
                //obtenerRegiones no es peticion AJAX
                obtenerRegiones();
                obtenerCategorias();
                obtenerProvincias();
                //obtenerCatalogos();
            });
         });

    </script>
@endsection

@extends('mgcp.layouts.app')

@section('cabecera')
    Descargar proformas
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Descargar</li>
        <li class="active">Proformas</li>
        <li class="active">Iniciar</li>
    </ol>
@endsection

@section('cuerpo')
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Configuración</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Empresa:</label>
                            <div class="col-sm-3">
                                <select class="form-control" id="selectEmpresa" name="empresa">
                                    @foreach($empresas as $empresa)
                                        <option value="{{$empresa->id}}">{{$empresa->empresa}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">Días de antiguedad:</label>
                            <div class="col-sm-3">
                                <input class="form-control" type="text" id="txtDias" name="dias" value="5">
                            </div>
                            <div class="col-sm-2">
                                <button id="btnIniciar" class="btn btn-primary">Iniciar</button>
                                <!--<button id="btnContinuar" class="btn btn-primary">Continuar</button>-->
                            </div>
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
            <div class="table-responsive">
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <th class="text-center">Acuerdos</th>
                        <th class="text-center">Catálogos</th>
                        <th class="text-center">Acuerdo actual</th>
                        <th class="text-center">Catálogo actual</th>
                        <th class="text-center">Tipo proforma actual</th>
                        <th class="text-center">Tipo contratación actual</th>
                        <th class="text-center">Progreso proformas</th>
                        <th class="text-center">Progreso total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td id="tdAcuerdos"></td>
                        <td id="tdCatalogos"></td>
                        <td id="tdAcuerdoActual" class="text-center"></td>
                        <td id="tdCatalogoActual" class="text-center"></td>
                        <td id="tdTipoProformaActual" class="text-center"></td>
                        <td id="tdTipoContratacionActual" class="text-center"></td>
                        <td id="tdProgresoProformas" class="text-center"></td>
                        <td id="tdProgresoTotal" class="text-center"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Data</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table style="width: 100%" class="table table-condensed table-hover">
                    <thead>
                    <tr>
                        <th class="text-center">N°</th>
                        <th class="text-center">N° proforma</th>
                        <th class="text-center">N° req.</th>
                        <th class="text-center">Proforma</th>
                        <th class="text-center">Requerimiento</th>
                        <th class="text-center">Entidad</th>
                        <th class="text-center">Fecha emisión</th>
                        <th class="text-center">Fecha límite</th>
                        <th class="text-center" style="width:25%">Producto</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Resultado</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyProformas"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ asset("mgcp/js/util.js") }}'></script>
    <script>
        $(document).ready(function () {
            Util.seleccionarMenu(window.location);

            //let acuerdos;
            let tiposContratacion = [
                {id: 0, descripcion: "Individual"},
                {id: 1, descripcion: "Paquete"}

            ];
            let tiposProforma = [
                {id: 'GRANCOMPRA', descripcion: "Gran compra"},
                {id: 'NORMAL', descripcion: "Compra ordinaria"}
            ];

            let $tdProgresoProformas = $('#tdProgresoProformas');
            let indiceAcuerdo = 0;
            let indiceCatalogo = 0;
            let indiceTipoProforma = 0;
            let indiceTipoContratacion = 0;
            let filaActual = 0;
            let totalFilas = 0;

            $('#btnContinuar').click(function () {
                filaActual=totalFilas;
            });

            $('#btnIniciar').click(function () {
                //indiceTipoContratacion = 0;
                $(this).prop('disabled', true);
                $('#selectEmpresa').prop('disabled', true);
                $('#tdAcuerdos').html('Obteniendo acuerdos...');
                $('#tdCatalogos').html('Obteniendo catálogos...');
                $('#tdAcuerdoActual').html('Por favor espere...');
                $('#tdCatalogoActual').html('Por favor espere...');
                $('#tdTipoProformaActual').html('Por favor espere...');
                $('#tdTipoContratacionActual').html('Por favor espere...');
                $('#tdProgresoProformas').html('Por favor espere...');
                $('#tdProgresoTotal').html('Por favor espere...');
                obtenerAcuerdos();
            });

            function obtenerAcuerdos() {
                $.ajax({
                    url: "{{route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos-local')}}",
                    type: 'post',
                    dataType: 'json',
                    data: {idEmpresa: $('#selectEmpresa').val(), pagina: 'proformas', _token: "{{csrf_token()}}"},
                    success: function (resultado) {
                        if (resultado.tipo == 'success') {
                            acuerdos = resultado.data;
                            let cadena = '<select disabled id="selectAcuerdos" class="form-control input-sm">';
                            for (let indice in resultado.data) {
                                cadena += '<option value="' + resultado.data[indice].id + '">' + resultado.data[indice].descripcion + '</option>';
                            }
                            cadena += '</select>';

                            $('#tdAcuerdos').html(cadena);
                            obtenerCatalogos();
                        } else {
                            $('#tdAcuerdos').html('<span class="text-' + resultado.tipo + '">' + resultado.mensaje + '</span>');
                        }

                    },
                    error: function () {
                        $('#tdAcuerdos').html('<span class="text-danger">Hubo un problema al obtener los acuerdos. Por favor actualice la página e inténtelo de nuevo</span>');
                    }
                });
            }

            function obtenerCatalogos() {
                if (indiceAcuerdo < $('#selectAcuerdos').find('option').length) {
                    $('#tdAcuerdoActual').html($('#selectAcuerdos').find('option:eq(' + indiceAcuerdo + ')').html());
                    $('#tdCatalogos').html('Obteniendo catálogos...');
                    $('#tdProgresoTotal').html('Acuerdo ' + (indiceAcuerdo + 1) + ' de ' + $('#selectAcuerdos').find('option').length);
                    $.ajax({
                        url: "{{route('mgcp.acuerdo-marco.peru-compras.obtener-catalogos')}}",
                        type: 'post',
                        data: {
                            idEmpresa: $('#selectEmpresa').val(),
                            idAcuerdo: $('#selectAcuerdos').find('option:eq(' + indiceAcuerdo + ')').val(),
                            _token: "{{csrf_token()}}"
                        },
                        success: function (resultado) {
                            if (resultado.tipo == 'success') {
                                let cadena = '<select disabled class="form-control input-sm" id="selectCatalogos">';
                                for (let indice in resultado.data) {
                                    cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>';//datos[indice].descripcion + ', ';
                                }
                                cadena += '</select>';
                                $('#tdCatalogos').html(cadena);
                                obtenerProformas();

                            } else {
                                $('#tdCatalogos').html('<span class="text-' + resultado.tipo + '">' + resultado.mensaje + '</span>');
                            }
                        },
                        error: function () {
                            obtenerCatalogos();
                        }
                    });
                } else {
                    $('#tdProgresoTotal').html('<span class="text-success">Fin del proceso</span>');
                    registrarDescarga();
                }
            }

            function registrarDescarga() {
                $.ajax({
                    url: "{{route('mgcp.acuerdo-marco.descargar.proformas.registrar-descarga')}}",
                    type: 'post',
                    data: {idEmpresa: $('#selectEmpresa').val(), _token: "{{csrf_token()}}"}
                });
            }

            function obtenerProformas() {
                if (indiceCatalogo < $('#selectCatalogos').find('option').length) {
                    if (indiceTipoProforma < tiposProforma.length) {
                        if (indiceTipoContratacion < tiposContratacion.length) {
                            //mostrarProgreso();
                            $('#tdCatalogoActual').html($('#selectCatalogos').find('option:eq(' + indiceCatalogo + ')').html());
                            $('#tdTipoProformaActual').html(tiposProforma[indiceTipoProforma].descripcion);
                            $('#tdTipoContratacionActual').html(tiposContratacion[indiceTipoContratacion].descripcion);

                            $('#tbodyProformas').html('<tr><td class="text-center" colspan="12">Obteniendo proformas...</td></tr>');
                            $.ajax({
                                url: "{{route('mgcp.acuerdo-marco.descargar.proformas.obtener-proformas')}}",
                                type: 'post',
                                data: {
                                    idEmpresa: $('#selectEmpresa').val(),
                                    idAcuerdo: $('#selectAcuerdos').find('option:eq(' + indiceAcuerdo + ')').val(),
                                    idCatalogo: $('#selectCatalogos').find('option:eq(' + indiceCatalogo + ')').val(),
                                    tipoProforma: tiposProforma[indiceTipoProforma].id,
                                    tipoContratacion: tiposContratacion[indiceTipoContratacion].id,
                                    diasAntiguedad: $('#txtDias').val(),
                                    _token: "{{csrf_token()}}"
                                },
                                success: function (datos) {
                                    $('#tbodyProformas').html(datos.datos);
                                    totalFilas = $('#tbodyProformas').find('tr').length;
                                    filaActual = 0;
                                    procesar();
                                },
                                error: function () {
                                    console.log("ERROR al obtener proformas: idAcuerdo " + $('#selectAcuerdos').find('option:eq(' + indiceAcuerdo + ')').val() + ", idCatalogo " + $('#selectCatalogos').find('option:eq(' + indiceCatalogo + ')').val() + ", tipoProforma: " + tiposProforma[indiceTipoProforma].id);
                                    obtenerProformas();
                                }
                            });
                        } else {
                            indiceTipoContratacion = 0;
                            indiceTipoProforma++;
                            obtenerProformas();
                        }
                    } else {
                        indiceTipoProforma = 0;
                        indiceCatalogo++;
                        obtenerProformas();
                    }
                } else {
                    indiceCatalogo = 0;
                    indiceAcuerdo++;
                    obtenerCatalogos()
                }
            }

            function procesar() {
                if (filaActual < totalFilas) {
                    let $fila = $('#tbodyProformas').find('tr:eq(' + filaActual + ')');
                    $fila.find('td.resultado').html('Procesando...');
                    
                    $.ajax({
                        url: "{{route('mgcp.acuerdo-marco.descargar.proformas.procesar-proforma')}}",
                        type: 'post',
                        data: {
                            idEmpresa: $('#selectEmpresa').val(),
                            idAcuerdo: $('#selectAcuerdos').find('option:eq(' + indiceAcuerdo + ')').val(),
                            idCatalogo: $('#selectCatalogos').find('option:eq(' + indiceCatalogo + ')').val(),
                            tipoProforma: tiposProforma[indiceTipoProforma].id,
                            tipoContratacion: tiposContratacion[indiceTipoContratacion].id,

                            N_Proforma: $fila.find('td.nroProforma').html(),
                            N_Requerimento: $fila.find('td.nroRequerimiento').html(),
                            C_Proforma: $fila.find('td.proforma').html(),
                            C_Requerimento: $fila.find('td.requerimiento').html(),
                            C_Ruc: $fila.find('span.ruc').html(),
                            C_Entidad: $fila.find('span.entidad').html(),
                            N_EntidadIndicadorSemaforo: $fila.find('span.semaforo').html(),
                            C_FechaEmision: $fila.find('td.fechaEmision').html(),
                            C_FechLimCoti: $fila.find('td.fechaLimite').html(),
                            C_Ficha: $fila.find('td.fichaProducto').html(),
                            C_Estado: $fila.find('td.estado').html(),
                            _token: "{{csrf_token()}}"
                        },
                        success: function (datos) {
                            filaActual++;
                            $tdProgresoProformas.html('Procesada proforma ' + filaActual + ' de ' + totalFilas);
                            $fila.find('td.resultado').html('<span class="text-' + datos.tipo + '">' + datos.mensaje + '</span>');

                        },
                        error: function () {
                            console.log("ERROR al procesar: Proforma: " + $fila.find('td.nroProforma').html() + ", Requerimiento " + $fila.find('td.nroRequerimiento').html() + ",ficha "+$fila.find('td.fichaProducto').html()+". REINTENTANDO...");
                            $fila.find('td.resultado').html('<span class="text-danger">Error, reintentando...</span>');
                            $tdProgresoProformas.html('Error en proforma ' + filaActual + ' de ' + totalFilas + ', reintentando...');
                            //totalFilas=1;
                        },
                        complete: function () {
                            procesar();
                        }
                    });
                } else {
                    indiceTipoContratacion++;
                    filaActual = 0;
                    obtenerProformas();
                }
            }

            function mostrarProgreso() {
            }
        });
    </script>
@endsection

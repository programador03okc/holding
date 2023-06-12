@extends('mgcp.layouts.app')

@section('cabecera') Descargar proformas @endsection

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
                <div class="col-md-2">
                    <h5>Empresa</h5>
                    <select class="form-control" id="selectEmpresa" name="empresa">
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}" data-pc="{{ $empresa->id_pc }}">{{ $empresa->empresa }}</option>
                        @endforeach
                    </select>
                    <a href="javascript: obtenerAcuerdos();" class="d-block">Obtener acuerdos</a>
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Acuerdos vigentes <span class="pull-right text-success" id="MessageAcuerdo">En espera...</span></h5>
                            <select class="form-control" id="selectAcuerdo" disabled></select>
                        </div>
                        <div class="col-md-4">
                            <h5>Catálogos <span class="pull-right text-success" id="MessageCatalogo">En espera...</span></h5>
                            <select class="form-control" id="selectCatalogo" disabled></select>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-7">
                                    <h5>Días de antiguedad</h5>
                                    <input type="number" class="form-control text-center" id="txtDias" name="dias" value="5">
                                </div>
                                <div class="col-md-5 text-center">
                                    <button class="btn btn-primary btn-block btn-flat" id="btnIniciar" style="margin-top: 34px;">Iniciar</button>
                                </div>
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
            <h3 class="box-title">Lista de proformas</h3>
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
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>
    <script src="{{ asset('mgcp/js/util.js') }}"></script>
    <script src="{{ asset('mgcp/js/timer.js') }}"></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/peru-compras.js') }}"></script>
    <script>
        
        $(document).ready(function () {
            Util.seleccionarMenu(window.location);
            $(".sidebar-mini").addClass("sidebar-collapse");

            let tiposContratacion = [
                {id: 0, descripcion: "Individual"},
                {id: 1, descripcion: "Paquete"}
    
            ];
            let tiposProforma = [
                {id: "GRANCOMPRA", descripcion: "Gran compra"},
                {id: "NORMAL", descripcion: "Compra ordinaria"}
            ];
            
            let indiceAcuerdo = 0;
            let indiceCatalogo = 0;
            let indiceTipoProforma = 0;
            let indiceTipoContratacion = 0;
            let filaActual = 0;
            let totalFilas = 0;
            let $tbody = $('#tbodyProformas');
            let $tdProgresoProformas = $("#tdProgresoProformas");

            const peruCompras = new PeruCompras("{{ csrf_token() }}");
            const $selectEmpresa = $("#selectEmpresa");
            const $selectAcuerdos = $("#selectAcuerdo");
            const $selectCatalogos = $("#selectCatalogo");

            const $spanAcuerdos = $("#MessageAcuerdo");
            const $spanCatalogos = $("#MessageCatalogo");

            const $btnIniciar = $("#btnIniciar");
            $btnIniciar.prop("disabled", true);

            $("#btnIniciar").click(function () {
                $(this).prop("disabled", true);
                $("#selectEmpresa").prop("disabled", true);
                //$("#tdAcuerdoActual").html("Por favor espere...");
                $("#tdCatalogoActual").html("Por favor espere...");
                $("#tdTipoProformaActual").html("Por favor espere...");
                $("#tdTipoContratacionActual").html("Por favor espere...");
                $("#tdProgresoProformas").html("Por favor espere...");
                $("#tdProgresoTotal").html("Por favor espere...");
                registrarDescarga($selectEmpresa.val(), $selectAcuerdos.val(), $selectCatalogos.val(), $("#txtDias").val());
                obtenerProformas();
            });

            $selectEmpresa.on("change", function() {
                obtenerAcuerdos($(this).find("option:selected").val());
            });

            obtenerAcuerdos = (idEmpresa = 0) => {
                var route = "{{ route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos') }}";
                $idEmpresa = (idEmpresa > 0) ? idEmpresa : $selectEmpresa.val();

                $selectCatalogos.text("En espera...");
                $selectEmpresa.prop("disabled", true);
                $spanAcuerdos.text("Obteniendo acuerdos...");
                $btnIniciar.prop("disabled", true);

                $.when(peruCompras.obtenerAcuerdos($idEmpresa, "proformas", route)).then(function (respuesta) {
                    if (respuesta.tipo == "success") {
                        let acuerdos = "";
                        for (let indice in respuesta.data) {
                            acuerdos += `<option value="${ respuesta.data[indice].id }">${ respuesta.data[indice].descripcion }</option>`;
                        }
    
                        $selectEmpresa.prop("disabled", false);
                        $selectAcuerdos.html(acuerdos);
                        $selectAcuerdos.removeAttr("disabled");
                        $spanAcuerdos.text("");
                        obtenerCatalogos($selectAcuerdos.val());
                    }
                });
            }

            $selectAcuerdos.on("change", function() {
                obtenerCatalogos($(this).find("option:selected").val());
            });

            obtenerCatalogos = (idCatalogo) => {
                var route = "{{ route('mgcp.acuerdo-marco.peru-compras.obtener-catalogos') }}";
                var $idCatalogo = $("#selectAcuerdo option:selected").val();
                $spanCatalogos.text("Obteniendo catálogos...");
                
                $selectCatalogos.html("");
                $("#tdAcuerdoActual").html($selectAcuerdos.find(`option:eq(` + indiceAcuerdo + `)`).html());
                $("#tdProgresoTotal").html(`Acuerdo ` + (indiceAcuerdo + 1) + ` de ` + $selectAcuerdos.find("option").length);

                $.when(peruCompras.obtenerCatalogos($selectEmpresa.val(), idCatalogo, route)).then(function (respuesta) {
                    let catalogos = "";
                    if (respuesta.tipo == "success") {
                        for (let indice in respuesta.data) {
                            catalogos += `<option value="` + respuesta.data[indice].Value + `">` + respuesta.data[indice].Text + `</option>`;
                        }
                        
                        $btnIniciar.prop("disabled", false);
                        $selectCatalogos.html(catalogos);
                        $selectCatalogos.removeAttr("disabled");
                        $spanCatalogos.text("");
                    }
                });
            }

            registrarDescarga = (idEmpresa, idAcuerdo, idCatalogo, dias) => {
                $.ajax({
                    url: "{{route('mgcp.acuerdo-marco.descargar.proformas.registrar-descarga')}}",
                    type: "post",
                    data: {idEmpresa: idEmpresa, idAcuerdo: idAcuerdo, idCatalogo: idCatalogo, dias: dias, _token: "{{csrf_token()}}"}
                });
            }

            obtenerProformas = () => {
                var route = "{{ route('mgcp.acuerdo-marco.descargar.proformas.obtener-proformas') }}";
                var idEmpresa = $selectEmpresa.val();
                var idAcuerdo = $selectAcuerdos.val();
                var idCatalogo = $selectCatalogos.val();
                var dias =  $("#txtDias").val();

                if (indiceTipoProforma < tiposProforma.length) {
                    if (indiceTipoContratacion < tiposContratacion.length) {
                        $('#tdCatalogoActual').html($selectCatalogos.find('option:eq(' + indiceCatalogo + ')').html());
                        $('#tdTipoProformaActual').html(tiposProforma[indiceTipoProforma].descripcion);
                        $('#tdTipoContratacionActual').html(tiposContratacion[indiceTipoContratacion].descripcion);
                        $('#tbodyProformas').html('<tr><td class="text-center" colspan="11">Obteniendo proformas...</td></tr>');

                        $.when(peruCompras.obtenerProformas(idEmpresa, idAcuerdo, idCatalogo, tiposProforma[indiceTipoProforma].id, tiposContratacion[indiceTipoContratacion].id, dias, route))
                        .then(function (respuesta) {
                            if (respuesta.filas > 1) {
                                $tbody.html(respuesta.datos);
                                totalFilas = $tbody.find('tr').length;
                                filaActual = 0;
                                procesar();
                            } else {
                                $tbody.html(`<tr><td colspan="11" class="text-center">No se encontraron proformas</td></tr>`);
                                $tdProgresoProformas.html("");
                            }
                        }).fail(() => {
                            console.log("ERROR al obtener proformas: idAcuerdo " + $('#selectAcuerdos').find('option:eq(' + indiceAcuerdo + ')').val() + ", idCatalogo " + $('#selectCatalogos').find('option:eq(' + indiceCatalogo + ')').val() + ", tipoProforma: " + tiposProforma[indiceTipoProforma].id);
                            obtenerProformas();
                        });
                    }
                }
            }

            procesar = () => {
                if (filaActual < totalFilas) {
                    let $fila = $tbody.find(`tr:eq(` + filaActual + `)`);
                    $fila.find("td.resultado").html("Procesando...");
                    
                    $.ajax({
                        url: "{{route('mgcp.acuerdo-marco.descargar.proformas.procesar-proforma')}}",
                        type: "post",
                        data: {
                            idEmpresa: $selectEmpresa.val(),
                            idAcuerdo: $selectAcuerdos.find(`option:eq(` + indiceAcuerdo + `)`).val(),
                            idCatalogo: $selectCatalogos.find(`option:eq(` + indiceCatalogo + `)`).val(),
                            tipoProforma: tiposProforma[indiceTipoProforma].id,
                            tipoContratacion: tiposContratacion[indiceTipoContratacion].id,

                            N_Proforma: $fila.find("td.nroProforma").html(),
                            N_Requerimento: $fila.find("td.nroRequerimiento").html(),
                            C_Proforma: $fila.find("td.proforma").html(),
                            C_Requerimento: $fila.find("td.requerimiento").html(),
                            C_Ruc: $fila.find("span.ruc").html(),
                            C_Entidad: $fila.find("span.entidad").html(),
                            N_EntidadIndicadorSemaforo: $fila.find("span.semaforo").html(),
                            C_FechaEmision: $fila.find("td.fechaEmision").html(),
                            C_FechLimCoti: $fila.find("td.fechaLimite").html(),
                            C_Ficha: $fila.find("td.fichaProducto").html(),
                            C_Estado: $fila.find("td.estado").html(),
                            _token: "{{csrf_token()}}"
                        },
                        success: function (datos) {
                            filaActual++;
                            $tdProgresoProformas.html('Procesada proforma ' + filaActual + ' de ' + totalFilas);
                            $fila.find('td.resultado').html('<span class="text-' + datos.tipo + '">' + datos.mensaje + '</span>');

                        },
                        error: function () {
                            $fila.find('td.resultado').html('<span class="text-danger">Error, reintentando...</span>');
                            $tdProgresoProformas.html('Error en proforma ' + filaActual + ' de ' + totalFilas + ', reintentando...');
                        },
                        complete: function () {
                            procesar();
                        }
                    });
                } else {
                    indiceTipoContratacion++;
                    indiceTipoProforma++;
                    filaActual = 0;
                    $("#tdProgresoTotal").html(`<span class="text-success">Fin del proceso</span>`);
                    obtenerProformas();
                }
            }
        });
    </script>
@endsection
